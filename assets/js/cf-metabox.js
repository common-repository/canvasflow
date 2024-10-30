var hostUrl = ajax_object.ajax_url;

window.onload = function() {
	if (document.getElementById('cf_publish') && document.getElementById('cf_publish') != null) {
		var publishBtn = document.getElementById('cf_publish');
		publishBtn.onclick = postArticle;
	}
}

/*if (document.getElementById('cf_publish') && document.getElementById('cf_publish') != null) {
	var publishBtn = document.getElementById('cf_publish');
	publishBtn.onclick = postArticle;
}*/

function sendArticle(data, hostUrl) {
    loadProcess();

    var urlEncodedData = "";
    var urlEncodedDataPairs = [];
    for (key in data) {
        urlEncodedDataPairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(data[key]));
    }

    urlEncodedData = urlEncodedDataPairs.join('&').replace(/%20/g, '+');

    var xhr = new XMLHttpRequest();
    // xhr.open('POST', '/wp-admin/admin.php?page=wp-canvasflow-plugin');
    xhr.open('POST', hostUrl);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.addEventListener('load', function (event) {
        successOnPublish();
    });
    xhr.addEventListener('error', function (event) {
        console.error(event);
        errorOnPublish();
    });
    xhr.send(urlEncodedData);
}

function loadProcess() {
    var cfAlert = document.getElementById('cf-alert');
    cfAlert.className = 'meta-box-alert meta-box-alert-processing';
    cfAlert.innerHTML = '<div class="loader"></div>';

    var publishBtn = document.getElementById('cf_publish');
    publishBtn.innerHTML = 'Processing';
}

function successOnPublish() {
    document.getElementById('cf_publish').innerHTML = 'Publish to Canvasflow';

    var cfState = document.getElementById('cf_state');
    cfState.className = 'meta-box-post-state-in-sync';
    cfState.innerHTML = 'In Sync';

    var cfAlert = document.getElementById('cf-alert');
    cfAlert.className = 'meta-box-alert meta-box-alert-success';
    cfAlert.innerHTML = '&#10004; Success';
}

function errorOnPublish() {
    document.getElementById('cf_publish').innerHTML = 'Publish to Canvasflow';

    // Log the error to the console
    var cfAlert = document.getElementById('cf-alert');
    cfAlert.className = 'meta-box-alert meta-box-alert-error';
    cfAlert.innerHTML = '&#215; Error';
}

function postArticle() {
    var PostID = document.getElementById("cf_post_id").value;
    var StyleID = document.getElementById("cf_style_id").value;
    var IssueID = document.getElementById("cf_issue_id").value;
    var nonce = document.getElementById("cf_nonce_send_article").value;
    var CollectionID = document.getElementById("cf_collection_id").value;
    var data = {
        id: PostID,
        style_id: StyleID,
        issue_id: IssueID,
        collection_id: CollectionID,
        action: 'send_to_cf_action',
        cf_nonce_send_article: nonce
	};
    loadProcess();
    jQuery.ajax({
        type: 'POST',
        url: hostUrl,
        data: data,
        success: function (result, status, xhr) {
            successOnPublish();
        },
        error: function (xhr, status, error) {
            errorOnPublish();
        }
    });
}