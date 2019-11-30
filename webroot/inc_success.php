<?php if (!defined('GUEST')) die('go away'); ?>

<div style="font-size:80%; position:fixed; top:5px; right:5px; z-index:23;"><a href="logout.php">Logout</a></div>


<div class="container">
    <div class="jumbotron">
        <a href="https://wiki.braveineve.com" target="_blank"><img src="img/brave.png" class="pull-right" alt=""></a>
        <h1>Welcome!</h1>
        <p>
            Hi <?php echo htmlentities($_SESSION['character_name']); ?>,<br>
            you have successfully authenticated your character. Follow the steps below to sign-up for <a
                    href="https://brave-collective.slack.com"
                    target="_blank">https://brave-collective.slack.com</a>.<br>
            <span style="font-size:70%;">
		        It is strongly recommened to <a href="https://slack.com/downloads" target="_blank">install</a>
                the slack desktop and mobile clients.
	        </span>
        </p>
        <br>
    </div>
    <div class="row">

        <div class="col-xs-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Invite</h3>
                </div>
                <div class="panel-body">
                    <div id="inviteFailed" hidden>
                        <p style="text-align:center;font-size:120%;" class="text-danger">
                            <b>Invitation failed, try again ...</b>
                        </p>
                    </div>
                    <div id="inviteSuccess" hidden>
                        <p style="text-align:center;font-size:120%;" class="text-success">
                            Invitation has been sent.
                        </p>
                    </div>
                    <div id="inviteComplete" hidden>
                        <p style="text-align:center;font-size:120%;" class="text-success">
                            You have signed up!
                        </p>
                    </div>
                    <div id="inviteLocked" hidden>
                        <p style="text-align:center;font-size:120%;" class="text-danger">
                            You have to wait at least 24 hours to request another invite...
                        </p>
                    </div>
                    <div id="invitePending" hidden>
                        <p style="text-align:center;">
                            Request an invite<br>
                            <br>
                            <input id='textMail' placeholder="Email Address" type="email">
                            <button type="submit" class="btn btn-primary btn-sm"
                                    onClick="invite('<?php echo $_SESSION['nonce']; ?>', $('#textMail').val());">
                                Invite
                            </button>
                        </p>
                        <p style="font-size:80%;">
                            Please be aware that this is a manual process - it can take <b>several days</b> for your
                            request to be processed<br>
                            The email address you use to sign-up will be visible to all slack members, don't <b>doxx
                                yourself</b> by accident.<br>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Sign-Up</h3>
                </div>
                <div class="panel-body">
                    <div id="signupComplete" hidden>
                        <p style="text-align:center;font-size:120%;" class="text-success">
                            You have signed up!
                        </p>
                    </div>
                    <div id="signupPending" hidden>
                        <p style="text-align:center;">
                            Wait for your invitation email to arrive and follow the steps to sign-up.<br>
                            Set your username and realname as close to your Eve character name as possible.<br>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Verify</h3>
                </div>
                <div class="panel-body">
                    <div id="verifyFailed" hidden>
                        <p style="text-align:center;font-size:120%;" class="text-danger">
                            <b>Verification failed, try again ...</b>
                        </p>
                    </div>
                    <div id="verifyComplete" hidden>
                        <p style="text-align:center;font-size:120%;" class="text-success">
                            You have verified your account!
                        </p>
                    </div>
                    <div id="verifyPending" hidden>
                        <p style="text-align:center;">
                            A few hours after signing up, you will receive a message asking to <b>verify</b> your
                            account.<br>
                            <br>
                            <input id="textCode" placeholder="Verification Code" type="text">
                            <button type="submit" class="btn btn-primary btn-sm"
                                    onClick="verify('<?php echo $_SESSION['nonce']; ?>', $('#textCode').val());">Verify
                            </button>
                        </p>
                        <p style="font-size:80%;">
                            Failing to verify your account, will get you <b>booted</b> from slack!<br>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script src="js/msc.js"></script>
