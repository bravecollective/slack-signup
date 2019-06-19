jQuery(document).ready(function() {
    update();
});

function update() {
    $.ajax({
        async: true,
        url: "status.php",
        mimeType: "application/json",
        dataType: 'json',
        error: function(xhr, status, error) {
        },
        success: function(json) {
	    if (json['verify_completed']) {
		$('#inviteComplete').prop("hidden", false);
		$('#signupComplete').prop("hidden", false);
		$('#verifyComplete').prop("hidden", false);
	    } else {
		if (json['invite_locked']) {
		    $('#inviteLocked').prop("hidden", false);
		} else {
		    $('#invitePending').prop("hidden", false);
		}
		$('#signupPending').prop("hidden", false);
		$('#verifyPending').prop("hidden", false);
	    }
        },
    });
}

function inviteClean() {
    $('#inviteSuccess').prop("hidden", true);
    $('#inviteFailed').prop("hidden", true);
    $('#invitePending').prop("hidden", true);
    $('#invitePending').prop("hidden", true);
    $('#inviteLocked').prop("hidden", true);
    $('#inviteComplete').prop("hidden", true);
}

function signupClean() {
    $('#signupPending').prop("hidden", true);
    $('#signupComplete').prop("hidden", true);
}

function verifyClean() {
    $('#verifySuccess').prop("hidden", true);
    $('#verifyFailed').prop("hidden", true);
    $('#verifyPending').prop("hidden", true);
    $('#verifyComplete').prop("hidden", true);
}

function invite(nonce, mail) {
    mail = mail.trim();
    if (mail == "") {
	return;
    }

    $.ajax({
	async: true,
	url: "invite.php?n=" + nonce + "&mail=" + mail,
	error: function(xhr, status, error) {
	    inviteClean();
	    $('#inviteFailed').prop("hidden", false);
	    $('#invitePending').prop("hidden", false);
	    signupClean();
	    $('#signupPending').prop("hidden", false);
	},
	success: function(json) {
	    inviteClean();
	    $('#inviteSuccess').prop("hidden", false);
	    $('#inviteLocked').prop("hidden", false);
	    signupClean();
	    $('#signupPending').prop("hidden", false);
        },
    });
}

function verify(nonce, code) {
    code = code.trim();
    if (code == "") {
	return;
    }

    $.ajax({
	async: true,
	url: "verify.php?n=" + nonce + "&code=" + code,
	error: function(xhr, status, error) {
	    verifyClean();
	    $('#verifyFailed').prop("hidden", false);
	    $('#verifyPending').prop("hidden", false);
	},
	success: function(json) {
	    inviteClean();
	    $('#inviteComplete').prop("hidden", false);
	    signupClean();
	    $('#signupComplete').prop("hidden", false);
	    verifyClean();
	    $('#verifyComplete').prop("hidden", false);
        },
    });
}
