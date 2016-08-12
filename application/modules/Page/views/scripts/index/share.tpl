<div id="fb-root"></div>
<?php
$settings = Engine_Api::_()->getDbTable('settings', 'core');

?>
<script>
  window.fbAsyncInit = function() {
    // init the FB JS SDK
    FB.init({
      appId      : <?php echo $settings->getSetting('inviter.facebook.consumer.key', false); ?>,                    // App ID from the app dashboard
      channelUrl : '//local.facebook-test/channel.html',  // Channel file for x-domain comms
      status     : true,                                 // Check Facebook Login status
      xfbml      : true,                                  // Look for social plugins on the page
      oauth      : true                                  // Enable oauth authentication
    });

    // Additional initialization code such as adding Event Listeners goes here

    FB.login(function(response)
    {
      console.log(response);
      if (response.authResponse)
      {
        console.log(response.authResponse.accessToken);


        /* SHARE STYLE POST TO WALL - START */
        var opts = {
         	message : 'Post message',
        	name : 'Test post to wall',
         	link : 'http://jonmosley.co.uk',
         	description : 'Description here',
         	picture : 'http://static.dezeen.com/uploads/2013/03/dezeen_Sergio-concept-car-by-Pininfarina_ss_4.jpg'
         };
         FB.api('/me/feed', 'post', opts, function(response)
         {
        	if (!response || response.error)
        	{
         		console.log(response.error);
         		alert(response.error.message);
         	}else{
         		alert('Success - Post ID: ' + response.id);
         	}
         });
        /* SHARE STYLE POST TO WALL - END */


        /* LARGE PHOTO STYLE POST TO WALL - START */
/*        var opts = {
          message : 'Photo upload',
          access_token: response.authResponse.accessToken,
          url : 'http://static.dezeen.com/uploads/2013/03/dezeen_Sergio-concept-car-by-Pininfarina_ss_4.jpg'
        };

        FB.api('/me/photos', 'post', opts, function(response)
        {
          if (!response || response.error)
          {
            console.log(response.error);
            alert('Posting error occured');
          }else{
            alert('Success - Post ID: ' + response.id);
          }
        });
        *//* LARGE PHOTO STYLE POST TO WALL - END */

      }else{
        alert('Not logged in');
      }
    }, { scope : 'publish_stream, user_photos, photo_upload' });

  };

  // Load the SDK asynchronously
  (function(d, s, id){
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) {window.fbAsyncInit();return;}
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/all.js";
    fjs.parentNode.insertBefore(js, fjs);
    window.fbAsyncInit();
  }(document, 'script', 'facebook-jssdk'));
</script>

