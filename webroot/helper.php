<?php
if (!defined('GUEST')) {
    die('go away');
}

// ------------------------------------------------------------------------

/**
 * @return bool|PDO
 */
function db_init()
{
    global $cfg_sql_url, $cfg_sql_user, $cfg_sql_pass;
    try {
        $dbr = new PDO($cfg_sql_url, $cfg_sql_user, $cfg_sql_pass);
    } catch (PDOException $e) {
        return false;
    }
    return $dbr;
}

/**
 * @param PDOStatement $stm
 * @param $code
 * @param $msg
 * @return bool
 */
function db_exec($stm, $code, $msg)
{
    if (!$stm->execute()) {
        $_SESSION['error_code'] = $code;
        $_SESSION['error_message'] = $msg;
        $arr = $stm->ErrorInfo();
        error_log('SQL failure:' . $arr[0] . ':' . $arr[1] . ':' . $arr[2]);
        return false;
    }

    return true;
}

// ------------------------------------------------------------------------

function sstart()
{
    session_start();

    if (!isset($_SESSION['nonce'])) {
        $_SESSION['nonce'] = krand(22);
    }
}

function sdestroy()
{
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }
    session_destroy();
}

function svalid()
{
    if (!isset($_SESSION['updated_at']) || $_SESSION['updated_at'] < (time() - 3600)) {
        $_SESSION['error_code'] = 100;
        $_SESSION['error_message'] = 'Session expired.';
        return false;
    }

    if (!isset($_SESSION['character_id']) || $_SESSION['character_id'] == 0) {
        $_SESSION['error_code'] = 101;
        $_SESSION['error_message'] = 'User not found.';
        return false;
    }

    return true;
}

function snonce()
{
    if (!isset($_SESSION['nonce'])) {
        return false;
    }
    if (!isset($_GET['n'])) {
        return false;
    }

    if ($_SESSION['nonce'] != $_GET['n']) {
        return false;
    }

    return true;
}

function serror()
{
    return isset($_SESSION['error_code']) && $_SESSION['error_code'] != 0;
}

// ------------------------------------------------------------------------

function krand($length)
{
    $alphabet = "abcdefghkmnpqrstuvwxyzABCDEFGHKMNPQRSTUVWXYZ23456789";
    $pass = "";
    for ($i = 0; $i < $length; $i++) {
        $pass = $pass . substr($alphabet, hexdec(bin2hex(openssl_random_pseudo_bytes(1))) % strlen($alphabet), 1);
    }
    return $pass;
}

function sendSlack($text, $receiver)
{
    global $cfg_user_agent, $cfg_slack_token, $cfg_slack_botname;

    if ($receiver == "@mombellicose7o") {
        return null;
    }

    $options = array(
        'http' => array(
            'method' => 'GET',
            'header' => array(
                'Host: slack.com',
                'User-Agent: ' . $cfg_user_agent,
            ),
        ),
    );
    $url = 'https://slack.com/api/chat.postMessage?' . 'token=' . urlencode($cfg_slack_token) .
        '&channel=' . urlencode($receiver) . '&text=' . urlencode($text) .
        '&as_user=false&username=' . urlencode($cfg_slack_botname);
    //dp($url);
    return file_get_contents($url, false, stream_context_create($options));
}

function dp($msg)
{
    $now = date('Y-m-d H:i:s');
    print($now . ': ' . $msg);
    if (strpos($msg, "\n") === FALSE) {
        print("\n");
    }
}

// ------------------------------------------------------------------------

function sso_update()
{
    global $cfg_ccp_client_id, $cfg_ccp_client_secret, $cfg_user_agent;

    // ---- Check parameters

    if (!isset($_GET['state'])) {
        $_SESSION['error_code'] = 10;
        $_SESSION['error_message'] = 'State not found.';
        return false;
    }
    $sso_state = $_GET['state'];

    if (!isset($_GET['code'])) {
        $_SESSION['error_code'] = 11;
        $_SESSION['error_message'] = 'Code not found.';
        return false;
    }
    $sso_code = $_GET['code'];

    if (!isset($_SESSION['nonce'])) {
        $_SESSION['error_code'] = 12;
        $_SESSION['error_message'] = 'Nonce not found.';
        return false;
    }
    $nonce = $_SESSION['nonce'];

    // ---- Verify nonce

    if ($nonce != $sso_state) {
        $_SESSION['error_code'] = 20;
        $_SESSION['error_message'] = 'Nonce is out of sync.';
        return false;
    }

    // ---- Translate code to token

    $data = http_build_query(
        array(
            'grant_type' => 'authorization_code',
            'code' => $sso_code,
        )
    );
    $options = array(
        'http' => array(
            'method' => 'POST',
            'header' => array(
                'Authorization: Basic ' . base64_encode($cfg_ccp_client_id . ':' . $cfg_ccp_client_secret),
                'Content-type: application/x-www-form-urlencoded',
                'Host: login.eveonline.com',
                'User-Agent: ' . $cfg_user_agent,
            ),
            'content' => $data,
        ),
    );
    $result = file_get_contents('https://login.eveonline.com/oauth/token', false, stream_context_create($options));

    if (!$result) {
        $_SESSION['error_code'] = 30;
        $_SESSION['error_message'] = 'Failed to convert code to token.';
        return false;
    }
    $sso_token = json_decode($result)->access_token;

    // ---- Translate token to character

    $options = array(
        'http' => array(
            'method' => 'GET',
            'header' => array(
                'Authorization: Bearer ' . $sso_token,
                'Host: login.eveonline.com',
                'User-Agent: ' . $cfg_user_agent,
            ),
        ),
    );
    $result = file_get_contents('https://login.eveonline.com/oauth/verify', false, stream_context_create($options));

    if (!$result) {
        $_SESSION['error_code'] = 40;
        $_SESSION['error_message'] = 'Failed to convert token to character.';
        return false;
    }

    $json = json_decode($result);
    $_SESSION['character_id'] = $json->CharacterID;
    #$owner_hash = $json->CharacterOwnerHash;

    // ---- Database

    $dbr = db_init();
    if (!$dbr) {
        $_SESSION['error_code'] = 50;
        $_SESSION['error_message'] = 'Failed to connect to the database.';
        return false;
    }

    // ---- Pull character

    if (!$person = pull_character($dbr, $_SESSION['character_id'])) {
        $_SESSION['error_code'] = 60;
        $_SESSION['error_message'] = 'Failed to update character.';
        return false;
    }
    $_SESSION = array_merge($_SESSION, $person);

    // ---- Success

    $_SESSION['error_code'] = 0;
    $_SESSION['error_message'] = 'OK';
    $_SESSION['updated_at'] = time();

    return true;
}

// ------------------------------------------------------------------------

/**
 * @param PDO $dbr
 * @param $character_id
 * @return bool
 */
function isInviteLocked($dbr, $character_id)
{
    $stm = $dbr->prepare('SELECT * FROM invite WHERE character_id = :character_id');
    $stm->bindValue(':character_id', $character_id);
    if (!$stm->execute()) {
        return true;
    }

    if (!$row = $stm->fetch()) {
        return false;
    }

    return $row['invited_at'] > (time() - 60 * 60 * 24);
}

/**
 * @param PDO $dbr
 * @param $mail
 * @param $character_id
 * @return bool
 */
function emailAssignedToSamePlayer($dbr, $mail, $character_id)
{
    global $cfg_core_api;
    global $cfg_core_app_id;
    global $cfg_core_app_secret;

    $stm = $dbr->prepare('SELECT * FROM invite WHERE email = :email AND account_status = :account_status');
    $stm->bindValue(':email', $mail);
    $stm->bindValue(':account_status', "Active");
    if (!$stm->execute()) {
        return false;
    }

    $matched_core_character = false;

    $invite_data = $stm->fetchAll();

    if (empty($invite_data)) {
        return true;
    }

    foreach ($invite_data as $throwaway => $each_row) {

        $character_id = (int)$character_id;
        $core_request_url = $cfg_core_api . '/app/v1/characters/' . $character_id;

        $core_bearer = base64_encode($cfg_core_app_id . ':' . $cfg_core_app_secret);

        $core_options = ["http" => ["method" => "GET", "header" => "Authorization: Bearer " . $core_bearer]];
        $core_context = stream_context_create($core_options);
        $core_response = file_get_contents($core_request_url, false, $core_context);

        $core_status_code = $http_response_header[0];

        if ($core_status_code == "HTTP/1.1 200 OK") {
            $characters_json = json_decode($core_response, true);

            foreach ($characters_json as $each_character) {
                $core_character_id = $each_character['id'];
                if ($core_character_id == $each_row["character_id"]) {
                    $matched_core_character = true;
                }
            }
        } else {
            error_log("Core error on $character_id: $core_status_code");
            return false;
        }
    }

    return $matched_core_character;

}

/**
 * @param PDO $dbr
 * @param $mail
 * @return bool
 */
function invite($dbr, $mail)
{
    global $cfg_slack_admin;

    if (isInviteLocked($dbr, $_SESSION['character_id'])) {
        return false;
    }

    if (!emailAssignedToSamePlayer($dbr, $mail, $_SESSION['character_id'])) {
        return false;
    }

    $stm = $dbr->prepare('SELECT * FROM invite WHERE character_id = :character_id');
    $stm->bindValue(':character_id', $_SESSION['character_id']);
    if (!$stm->execute()) {
        return false;
    }

    if ($row = $stm->fetch()) {
        $stm = $dbr->prepare(
            'UPDATE invite 
            SET email = :email, invited_at = :invited_at, email_history = :email_history 
            WHERE character_id = :character_id'
        );
        $stm->bindValue(':character_id', $_SESSION['character_id']);
        $stm->bindValue(':email', $mail);
        $stm->bindValue(':invited_at', time());
        $stm->bindValue(':email_history', $row['email_history'] . ", " . $row['email']);
        if (!$stm->execute()) {
            return false;
        }
    } else {
        $stm = $dbr->prepare(
            'INSERT INTO invite (character_id, character_name, email, email_history, invited_at) 
            VALUES (:character_id, :character_name, :email, "", :invited_at)'
        );
        $stm->bindValue(':character_id', $_SESSION['character_id']);
        $stm->bindValue(':character_name', $_SESSION['character_name']);
        $stm->bindValue(':email', $mail);
        $stm->bindValue(':invited_at', time());
        if (!$stm->execute()) {
            return false;
        }
    }

    sendSlack($_SESSION['character_name'] . ' <' . $mail . '>', $cfg_slack_admin);

    return true;
}

// ------------------------------------------------------------------------

/**
 * @param PDO $dbr
 * @param $character_id
 * @return bool
 */
function isVerifyCompleted($dbr, $character_id)
{
    $stm = $dbr->prepare('SELECT * FROM invite WHERE character_id = :character_id AND slack_id IS NOT NULL');
    $stm->bindValue(':character_id', $character_id);
    if (!$stm->execute()) {
        return false;
    }

    return $row = $stm->fetch();
}

function getLinkedCharacter($dbr, $character_id) {

    $stm = $dbr->prepare('SELECT * FROM invite WHERE character_id = :character_id AND slack_id IS NOT NULL');
    $stm->bindValue(':character_id', $character_id);
    if (!$stm->execute()) {
        return false;
    }
    
    $invite_data = $stm->fetchAll();
    
    if (empty($invite_data)) {
        return false;
    }

    foreach ($invite_data as $throwaway => $each_row) {
        
        $email_to_check = $each_row["email"];
        
    }
    
    $stm = $dbr->prepare('SELECT * FROM invite WHERE email = :email AND slack_id IS NOT NULL');
    $stm->bindValue(':email', $email_to_check);
    if (!$stm->execute()) {
        return false;
    }
    
    $link_data = $stm->fetchAll();
    
    if (empty($link_data)) {
        return false;
    }
    
    $time_to_beat = 0;
    $linked_character_data = [];

    foreach ($link_data as $throwaway => $each_row) {
        
        if ($each_row["invited_at"] > $time_to_beat) {
            
            $linked_character_data["character_link"] = "https://evewho.com/character/" . $each_row["character_id"];
            $linked_character_data["character_name"] = $each_row["character_name"];
            
            $time_to_beat = $each_row["invited_at"];
            
        }
    }
    
    return $linked_character_data;
}

// ------------------------------------------------------------------------

/**
 * @param PDO $dbr
 * @param $character_id
 * @return array|bool
 */
function pull_character($dbr, $character_id)
{
    #global $cfg_user_agent;

    $person = array();

    /*$options = array(
        'http' => array(
            'method' => 'GET',
            'header' => array(
                'Host: api.eveonline.com',
                'User-Agent: ' . $cfg_user_agent,
            ),
        ),
    );*/
    $result = file_get_contents('https://esi.evetech.net/latest/characters/' . $character_id);
    if ($result) {
        $characterData = json_decode($result, true);
        $person['character_id'] = $character_id;
        $person['character_name'] = $characterData['name'];
        $person['corporation_id'] = $characterData['corporation_id'];
    } else {
        return false;
    }

    $result = file_get_contents('https://esi.evetech.net/latest/corporations/' . $person['corporation_id']);
    if ($result) {
        $corporationData = json_decode($result, true);
        $person['corporation_name'] = $corporationData['name'];
        $person['corporation_ticker'] = $corporationData['ticker'];
        $person['alliance_id'] = isset($corporationData['alliance_id']) ? $corporationData['alliance_id'] : null;
        $person['alliance_name'] = null;
        $person['faction_id'] = null;
        $person['faction_name'] = null;
    } else {
        return false;
    }

    $groups = core_groups(array($character_id));
    if (isset($groups[$character_id])) {
        if (!hasSlackPermission($groups[$character_id])) {
            return false;
        }
        $person['core_groups'] = $groups[$character_id];
        $person['core_tags'] = '';
        $person['core_perms'] = '';
    } else {
        return false;
    }

    return $person;
}

/**
 * Grab core groups
 *
 * TODO need a bulk query endpoint in core to avoid all these queries
 *
 * @param $full_character_id_array
 * @return array|bool
 */
function core_groups($full_character_id_array)
{
    global $cfg_core_api;
    global $cfg_core_app_id;
    global $cfg_core_app_secret;

    $groups = [];

    if (isset($cfg_core_api) and isset($cfg_core_app_id) and isset($cfg_core_app_secret)) {
        $core_bearer = base64_encode($cfg_core_app_id . ':' . $cfg_core_app_secret);

        foreach ($full_character_id_array as $character_id) {
            $character_id = (int)$character_id;
            $core_request_url = $cfg_core_api . '/app/v2/groups/' . $character_id;

            $core_options = ["http" => ["method" => "GET", "header" => "Authorization: Bearer " . $core_bearer]];
            $core_context = stream_context_create($core_options);
            $core_response = file_get_contents($core_request_url, False, $core_context);

            $core_status_code = $http_response_header[0];

            if ($core_status_code == "HTTP/1.1 200 OK") {
                $char_groups_json = json_decode($core_response, True);
                $char_groups = [];

                foreach ($char_groups_json as $char_group) {
                    $group_name = $char_group['name'];
                    $char_groups[] = $group_name;
                }
                $groups[$character_id] = implode(',', $char_groups);
            } else {
                error_log("Core error on $character_id: $core_status_code");
                return False;
            }
        }
    }
    return $groups;
}

function hasSlackPermission($groups)
{
    if (strpos($groups, 'family') === false && strpos($groups, 'member') === false) {
        return false;
    }

    return true;
}

?>