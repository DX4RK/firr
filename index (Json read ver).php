<?php
//Needed varibles

//you need to put a roblox cookie here blame roblox
$cookie = "_|WARNING:-DO-NOT-SHARE-THIS.--Sharing-this-will-allow-someone-to-log-in-as-you-and-to-steal-your-ROBUX-and-items.|_E8A4E5408DF7A799801D942AA5E26183C2281B0AA1647F0D15F729D78CD5BFA80FE0AFC37E27EF7E316683993120AC6B8A47940D9E86BDE4EAE5520E182CF1763D0403DE1D635F8AC557A74F236A1BAB0BCEE39BFED813209F1888D647EB59C856E3216A95F6DC80ABD07FF048EA46E3AE1B6D0D35D1119C41B0025FA17FA2584EE5EE6AFBD0EE3E9437BE7B4FE76F7BEB258C9E0235011920AB05AB8EB00229C7D54E634EDBB3F76EA9F025B1BA1EBECB7A7B42E61AFE58D5DB849491A76484D8A4947F83A8AAFC1BE717FF2AA3517E7383B4A7A649D0FA44FA3C9FF52A1196BC0D29E82AC3ADC6579BB7B074DB1CBBBC75B4B1B435F8C1E9AE3BA5B725328580ED551F3DB4B9CBEEA62FFC7A1B06621BC92FBA44D1876178E03C531CA34C2AA194026C035BD6CF676C468E1EAA14144A75C8D72056933B12FA226C37FBCAF51930E2C19BE00F5A39B9E7AA76CD866BDA5FA1F0";
$pageName = "Condos";
$blurAmount = "10px";
$backgroundImage = "https://www.teahub.io/photos/full/11-111196_gif-wallpaper.gif";
$discordInvite = "https://discord.gg/";
$iconUrl = "https://images.rbxcdn.com/3b43a5c16ec359053fef735551716fc5.ico"; // Icon of the site

$githubCredits = True; //Add in the bottom right my github link

//Embed data
$enableEmbed = True; //IDK if i made the toggle right lol
$embedHexColor = "#85bb65"; //Needs to be hex code
$embedTitle = "Condos"; //Title for embed
$embedDescription = "List of Condos"; //Description for embed

#region Discord out of games and error webhook
function postToDiscord($message,$name,$avatarUrl){
    $json_data = json_encode(["content" => $message, "username" => $name, "avatar_url" => $avatarUrl], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
    $ch = curl_init( "https://discord.com/api/webhooks/894615985334661212/-3VTGFRkmYejbReiEp3SHXJ50ZxvKHe-YG5hvnJIL7zQ7dgS0qKWK7HUYuSFHn6ZqnDr" );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    curl_setopt( $ch, CURLOPT_POST, 1);
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data);
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt( $ch, CURLOPT_HEADER, 0);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
    
    $response = curl_exec( $ch );
    curl_close( $ch );
}
#endregion


try {
    $strJsonFileContents = file_get_contents("gameIds.json");
    $array = json_decode($strJsonFileContents, true);
    $gameIds = $array["gameIds"];
    shuffle($gameIds);
    $bannedCount = 0; //set count to 0 to beable to count bans later 
    $gameAmountCount = count($gameIds); //Get the ammount of games in list
} catch (Error $e) {}

function sendCsrfRequest(){ //Send a request to get the CSRF token from roblox
    $csrfUrl = "https://auth.roblox.com/v2/login";

    function grabCsrfToken( $curl, $header_line ) { //Filter through the Roblox headers
        if(strpos($header_line, "x-csrf-token") !== false){
            global $csrf;
            $csrf = ltrim($header_line, "x-csrf-token: "); // set x-csrf-token var
        }
        return strlen($header_line);
    }

    $csrfCurl = curl_init();
    curl_setopt($csrfCurl, CURLOPT_URL, $csrfUrl);
    curl_setopt($csrfCurl, CURLOPT_POST, true);
    curl_setopt($csrfCurl, CURLOPT_HEADERFUNCTION, "grabCsrfToken");
    curl_setopt($csrfCurl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($csrfCurl,CURLOPT_RETURNTRANSFER,1);

    curl_exec($csrfCurl);
    curl_close($csrfCurl);
}

function checkGame($placeId){ //Finds what game works
    global $csrf, $cookie, $isPlayable;
    $gameUrl = "https://games.roblox.com/v1/games/multiget-place-details?placeIds=$placeId";

    $gameCurl = curl_init();
    curl_setopt($gameCurl, CURLOPT_URL, $gameUrl);

    $headers = array("X-CSRF-TOKEN: ".$csrf);
    curl_setopt($gameCurl, CURLOPT_COOKIE, '.ROBLOSECURITY='.$cookie);
    curl_setopt($gameCurl, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($gameCurl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($gameCurl, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    curl_setopt($gameCurl, CURLOPT_RETURNTRANSFER,1);

    $resp = curl_exec($gameCurl);
    curl_close($gameCurl);
    $data = json_decode($resp);
    return $data[0]->isPlayable; //Get if you can play or not
}

try {
    sendCsrfRequest();
} catch (Error $e) {}
$versionId = "1.0.2"
?>
<!DOCTYPE html>
<html lang='en'>
	<head>
		<meta charset='UTF-8'/>
		<title>
                <?php echo($pageName); ?>
		</title>
        <link rel="icon" href="<?php echo($iconUrl); ?>">
	    <style>
	    	@import url('https://fonts.googleapis.com/css?family=Montserrat&display=swap');
	    	@import url('https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css');

	    	@keyframes glowing {
	    		0% {
                    filter: drop-shadow(0 0 0.25rem red);
	    		}
	    		50% {
                    filter: drop-shadow(0 0 0.50rem green);
	    		}
	    		100% {
                    filter: drop-shadow(0 0 0.25rem red);
	    		}
	    	}

	    	body {
    			background: url("<?php echo($backgroundImage); ?>") no-repeat center center fixed; 
				background-repeat: no-repeat;
				backdrop-filter: blur(<?php echo($blurAmount); ?>);
				background-position: bottom;
				background-size: cover;

				height: 100vh;
				width: 100%;

	    		font-family: 'Montserrat', sans-serif;
	    		min-height: 80vh;
	    		display: -webkit-box;
	    		display: flex;
	    		align-items: center;
	    		justify-content: center;
	    		flex-direction: column;
	    	}

	    	h1 {
	    		font-family: 'Montserrat', sans-serif !important;
	    		font-weight: bold;
                filter: drop-shadow(0 0 0);
	    		animation: glowing 3500ms infinite;
	    	}

	    	h2 {
	    		font-family: 'Montserrat', sans-serif !important;
	    		font-size: 350%;
	    	}

	    	h3 {
	    		font-family: 'Montserrat', sans-serif !important;
	    		font-size: 150%;
	    	}

            #bottomRight
            {
                position:fixed;
                bottom:5px;
                right:5px;
                opacity:0.5;
                z-index:99;
                color:white;
            }
            #bottomLeft
            {
                position:fixed;
                bottom:5px;
                left:5px;
                opacity:0.5;
                z-index:99;
                color:white;
            }
	    </style>

        <script>
            function fadeInPage() {
                for (let i = 1; i < 100; i++) {
                    fadeIn(i * 0.01);
                }
            
                function fadeIn(i) {
                    setTimeout(function() {
                        document.body.style.opacity = i;
                    }, 2000 * i);
                }
            }
        </script>
        <?php if ($enableEmbed): ?>
        <meta name="description" content="<?php echo($embedDescription);?>">

        <!-- Google / Search Engine Tags -->
        <meta name="theme-color" content="<?php echo($embedHexColor);?>">
        <meta itemprop="name" content="<?php echo($embedTitle);?>">
        <meta itemprop="description" content="<?php echo($embedDescription);?>">

        <!-- Facebook Meta Tags -->
        <meta property="og:title" content="<?php echo($embedTitle);?>">
        <meta property="og:type" content="website">

        <!-- Twitter Meta Tags -->
        <meta name="twitter:card" content="summary">
        <meta name="twitter:title" content="<?php echo($embedTitle);?>">
        <meta name="twitter:description" content="<?php echo($embedDescription);?>">
        <?php endif; ?>
	</head>

	<body style="opacity:0" onload='fadeInPage()'>
		<h1 class="text-light">
                <?php echo($pageName); ?>
		</h1>
        <div class="btn-group mt-2 mb-4" role="group" aria-label="actionButtons">
			<a href="<?php echo($discordInvite); ?>" class="d-block btn btn-outline-light">
				Join the Discord
			</a>
		</div>
        <a>
            <?php
                try {
                    foreach ($gameIds as $gameId) {
                        echo "<h3 class=\"text-light\">";
                        $isPlayable = checkGame($gameId);
                        echo "<b>$gameId:</b> ";
                        if ($isPlayable){
                            echo "<a style=\"color: #dcdcdc\" href=\"https://www.roblox.com/games/$gameId\"><u>Click for game</u></a>";
                        }else{
                            echo "Game banned";
                            $bannedCount += 1;
                        }
                        echo "{$bannedCount}/{$gameAmountCount}<br></h3>";
                    }
                } catch (Error $e) {
                }
                if($bannedCount == $gameAmountCount){
                    if ($array["hasPinged"] == false){
                        $array["hasPinged"] = true;
                        $newJsonString = json_encode($array);
                        file_put_contents('gameIds.json', $newJsonString);
                        postToDiscord("All {$gameAmountCount} game(s) are banned on the [site](https://cashmoney-con.tk/)!", "Game ID banned!","https://static3.depositphotos.com/1001097/123/i/600/depositphotos_1238353-stock-photo-forbidden-sign.jpg");
                    }
                }
            ?>
            <div id="bottomLeft">
                V<?php echo($versionId); ?>
            </div>
            <?php if ($githubCredits): ?>
            <div id="bottomRight">
                <a href="https://github.com/Roblox-Thot/cashmoney-con.tk">
                    Site coded by Roblox Thot
                </a>
            </div>
            <?php endif; ?>
		</a>
    </body>
</html>
