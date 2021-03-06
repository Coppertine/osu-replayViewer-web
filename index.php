<?php
session_start();
require 'secure/uploadKey.php';
?>
<!DOCTYPE html>
<html>
  <head>
    <!-- Discord metadata -->
    <meta content="osu!replayViewer - osu! replays sharing" property="og:title">
    <meta content="Share your osu! performance to everyone !" property="og:description">
    <meta content="osu!replayViewer" property="og:site_name">
    <meta content="http://osureplayviewer.xyz/images/icon.png" property='og:image'>

    <title>osu!replayViewer - A online osu replay viewer</title>
    <link rel="stylesheet" type="text/css" href="css/index.css">
    <link rel="stylesheet" type="text/css" href="css/navbar.css">
    <link rel="stylesheet" type="text/css" href="css/footer.css">
    <link rel="stylesheet" type="text/css" href="css/loader.css">
    <link rel="icon" type="image/png" href="images/icon.png" />

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/modernizr/2.8.2/modernizr.js"></script>
    <script src="js/loader.js"></script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <script src="js/index/askUsername.js"></script>
    <!-- Cookie bar -->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/cookie-bar/cookiebar-latest.min.js?theme=flying&tracking=1&always=1&scrolling=1"></script>
  </head>

  <body onload="start()">
    <script type="text/javascript" src="js/index/upload.js"></script>
    <div class="loader"></div>
    <!-- Modal -->
    <div id="myModal" class="modal">

      <!-- Modal content -->
      <div class="modal-content">
        <h2 class="modal_title">Informations about your replay</h2>

        <div id="replay_box">
          <div>
            <?php
              if(isset($_SESSION['beatmapSetId'])){
                $url = "https://b.ppy.sh/thumb/".$_SESSION['beatmapSetId']."l.jpg";
                echo "<img src='".$url."'/>";
              }else{
                echo '<img src="images/preview.jpg"/>';
              }
            ?>
          </div>

          <div id="replay_text">
            <span id="beatmap"><span class="info_text">Beatmap :</span>
              <?php
                echo $_SESSION['beatmapName'];
              ?>
            </span>

            <span id="diff"><span class="info_text">Difficulty :</span>
              <?php
                echo $_SESSION['difficulty'];
              ?>
            </span>
            <span id="player"><span class="info_text">Player :</span>
              <?php
                echo $_SESSION['playername'];
              ?>
            </span>
            <span id="mods"><span class="info_text">Mods :</span>
              <?php
                echo $_SESSION['mods'];
              ?>
            </span>
            <span id="skin"><span class="info_text">Skin :</span>
              <?php echo $_SESSION['skinName']; ?>
            </span>
            <span id="time"><span class="info_text">Duration :</span>
              <?php
              $mins = floor($_SESSION['duration'] / 60 % 60);
              $secs = floor($_SESSION['duration'] % 60);
                echo $mins.'min '.$secs;
              ?>
            </span>
          </div>
        </div>

        <h2 class="modal_title" >Checklist : </h2>
        <div id="check_list">
          <div class="modal_item" >
            <img src="images/index/modal1.png" id="replayS"/>
            <span class="modal_caption">Replay structure</span>
          </div>

          <div class="modal_item" >
            <img src="images/index/modal2.png" id="beatmapA"/>
            <span class="modal_caption">Beatmap is available</span>
          </div>

          <div class="modal_item" >
            <?php
            if($_SESSION['replay_playerId'] != null){
              echo "<img src='"."https://a.ppy.sh/".$_SESSION['replay_playerId']."' id=\"playerA\"/>";
            }else{
              echo '<img src="images/index/modal3.png" id="playerA"/>';
            }
            ?>
            <span class="modal_caption">The player has an <br>osu account</span>
          </div>

          <div class="modal_item" >
            <img src="images/index/modal4.png" id="replayD"/>
            <span class="modal_caption">Replay is under 10min</span>
          </div>

          <div class="modal_item" >
            <img src="images/index/modal5.png" id="replayDup"/>
            <span class="modal_caption">Replay is not a duplicate</span>
          </div>

          <div class="modal_item" >
            <img src="images/index/modal6.png" id="replayW"/>
            <span class="modal_caption">Replay is not in <br>waiting list</span>
          </div>

        </div>

        <div id="replay_start">
          <form class="align_center" method="post" enctype="multipart/form-data" action="php/index/upload.php">
            <input id="checkBox" type="checkbox" name="checkbox"> <span> do not delete my replay after 30 days</span><br>
            <input id="filename" name="filename" type="hidden" value=<?php echo '"'.$_SESSION['filename'].'"' ?>>
            <input id="duration" name="duration" type="hidden" value=<?php echo '"'.$_SESSION['duration'].'"' ?>>
            <input id="duration" name="keyHash" type="hidden" value=<?php echo '"'.password_hash($upload_replay_key,PASSWORD_DEFAULT).'"' ?>>
            <input type="submit" value="Start processing" id="start_processing" onclick="clearSession()">
          </form>

          <button onclick="closeModal(); clearSession();">Cancel</button>
        </div>
      </div>

    </div>

    <div id="askUsername_modal" class="modal">

      <!-- Modal content -->
      <div class="modal-content">
        <h2 class="modal_title">Information needed !</h2>
        <h3 class="modal_par">The username for this replay doesn't exists</h3>
        <h3 class="modal_par">Please enter the username to which the replay will be assigned</h3>

        <form class="align_center" method="post" enctype="multipart/form-data" action="php/index/newUsername.php">
          <input type="text" name="newUsername" id="newUsername" onkeyup="showUsername(this.value)">

          <?php
            $newArray = array_diff_key($_SESSION,array_flip(array('username','userId')));
            $arraySerial = serialize($newArray);
            $array64 = base64_encode($arraySerial);
           ?>

          <h3 id="txtHint" class="align_center"></h3>

          <input type="hidden" name="session" value=<?php echo "'".$array64."'" ?>>
          <input type="submit" value="Continue" id="continue_btn">
        </form>

        <div id="replay_start">
          <button onclick="closeModalUsername(); clearSession();">Cancel</button>
        </div>
      </div>

    </div>

    <!-- Activate modal view when session is full -->
    <?php
      if(isset($_SESSION['replayStructure'])){

        $string = '';
        if(!$_SESSION['replayStructure']){$string .= "setItemFalse('replayS'); ";}
        if(!$_SESSION['beatmapAvailable']){$string .= "setItemFalse('beatmapA'); ";}
        if(!$_SESSION['playerOsuAccount']){$string .= "setItemFalse('playerA'); "; }
        if(!$_SESSION['replayBelow10']){$string .= "setItemFalse('replayD'); ";}
        if(!$_SESSION['replayNotDuplicate']){$string .= "setItemFalse('replayDup'); ";}
        if(!$_SESSION['replayNotWaiting']){$string .= "setItemFalse('replayW'); ";}

        if($_SESSION['playerOsuAccount']){
          echo '<script type="text/javascript">',
               'openModal();',
               '</script>';
        }else{
          echo '<script type="text/javascript">',
               'openModalUsername();',
               '</script>';
        }


        echo "<script type=\"text/javascript\">".$string."</script>";

        if(!$_SESSION['replayStructure'] || !$_SESSION['beatmapAvailable'] || !$_SESSION['playerOsuAccount'] || !$_SESSION['replayBelow10'] || !$_SESSION['replayNotDuplicate'] || !$_SESSION['replayNotWaiting']){
          echo '<script type="text/javascript">',
               'disableProcessing();',
               '</script>';
        }
      }
    ?>

    <!-- Top navigation bar -->
    <div class="top-nav">
      <div class="floatleft">
        <a href="search.php" class="nav-link">
          <i class="material-icons">search</i> Search</a>
        <a href="faq.php" class="nav-link">
          <i class="material-icons">question_answer</i> FAQ</a>
      </div>

      <a href="#" id="logo">
        <img src="images/icon.png" />
      </a>

      <?php
        if(isset($_SESSION['userId']) && isset($_SESSION['username'])){
          $userUrl = "userProfile.php?id=".$_SESSION['userId'];
          echo '<div class="floatright">';
          echo  "<a href=$userUrl class=\"nav-link\">";
          echo    '<i class="material-icons">how_to_reg</i> Profile</a>';
          echo  '<a href="logout.php" class="nav-link">';
          echo    '<i class="material-icons">vpn_key</i> Logout</a>';
          echo '</div>';
        }else{
          echo '<div class="floatright">';
          echo  '<a href="register.php" class="nav-link">';
          echo    '<i class="material-icons">how_to_reg</i> Register</a>';
          echo  '<a href="login.php" class="nav-link">';
          echo    '<i class="material-icons">vpn_key</i> Login</a>';
          echo '</div>';
        }
      ?>
    </div>

    <!-- presentation -->
    <h1 id="title"> osu!replayViewer </h1>

    <h2 id="slogan"> Share your osu! performance to everyone !</h2>

    <div id="etapes">
      <div class="item">
        <img src="images/etape1.png"/>
        <span class="caption">1. Upload your replay</span>
      </div>

      <div class="item">
        <img src="images/etape2.png"/>
        <span class="caption">2. Wait processing time</span>
      </div>

      <div class="item">
        <img src="images/etape3.png"/>
        <span class="caption">3. Share it !</span>
      </div>
    </div>

    <form action="#upload_section" class="align_center">
      <input type="submit" value="Begin !" class="button" />
    </form>

    <!-- Upload -->
    <section id="upload_section"> </section>

    <h2 id="upload_title">Select your osu replay to upload (.osr)</h2>
    <h2 id="upload_subtitle">Drag and drop or open the explorer</h2>

    <form action="php/index/replayFileVerf.php" method="post" enctype="multipart/form-data" id="upload_box">
        <input type="file" name="fileToUpload" id="fileToUpload" oninput="submitForm()">
    </form>

    <footer>
      <h3 class="align_center">osu!replayViewer is not affiliated with osu! - All credit to Dean Herbert</h3>
      <div class="footer_img">
        <a href="https://discord.gg/pqvhvxx" title="join us on discord!" target="_blank">
          <img src="images/index/discord_logo.png"/>
        </a>
        <a href="https://osu.ppy.sh/community/forums/topics/697883" target="_blank">
          <img src="images/index/osu forums.png"/>
        </a>
        <a href="https://github.com/codevirtuel/osu-replayViewer-web" target="_blank">
          <img src="images/index/github_logo.png"/>
        </a>
        <a href="https://paypal.me/codevirtuel" target="_blank">
          <img src="images/index/paypal_me.png"/>
        </a>
      </div>

      <div id="created">
        <span> website created by codevirtuel <a href="https://osu.ppy.sh/u/3481725" target="_blank"><img src="images/codevirtuel.jpg"/></a></span>
      </div>
    </footer>
  </body>

</html>
