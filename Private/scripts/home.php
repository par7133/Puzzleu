<?php

/**
 * Copyright 2021, 2024 5 Mode
 *
 * This file is part of Puzzleu.
 *
 * Puzzleu is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Puzzleu is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.  
 * 
 * You should have received a copy of the GNU General Public License
 * along with Puzzleu. If not, see <https://www.gnu.org/licenses/>.
 *
 * home.php
 * 
 * Home of Puzzleu.
 *
 * @author Daniele Bonini <my25mb@aol.com>
 * @copyrights (c) 2016, 2024, 5 Mode
 */

 // CONSTANTS AND VARIABLE DECLARATION      
 $CURRENT_VIEW = PUBLIC_VIEW;
 
 $CUDOZ = 1;
 
 $AVATAR_PATH = APP_DATA_PATH . DIRECTORY_SEPARATOR . AVATAR_NAME;

 $CV_PATH = $AVATAR_PATH . DIRECTORY_SEPARATOR . "cv";      
 $FRIENDS_PATH = $AVATAR_PATH . DIRECTORY_SEPARATOR . "friends";      
 $BLOG_PATH = $AVATAR_PATH . DIRECTORY_SEPARATOR . "blog";      
 $GALLERY_PATH = $AVATAR_PATH . DIRECTORY_SEPARATOR . "gallery";      
 $MAGICJAR1_PATH = $AVATAR_PATH . DIRECTORY_SEPARATOR . "magicjar1";      
 $MAGICJAR2_PATH = $AVATAR_PATH . DIRECTORY_SEPARATOR . "magicjar2";
 $MAGICJAR3_PATH = $AVATAR_PATH . DIRECTORY_SEPARATOR . "magicjar3";
 
 $profilePic = APP_DEF_PROFILE_PIC;
 
 
 // PAGE PARAMETERS
 $lang = APP_DEF_LANG;
 $lang1 = substr(strip_tags(filter_input(INPUT_GET, "hl")??""), 0, 5);
 if ($lang1 !== PHP_STR) {
   $lang = $lang1;
 }
 $shortLang = getShortLang($lang);
 
 $blogSP = (int)substr(strip_tags(filter_input(INPUT_GET, "blogSP")??""), 0, 5);
 
 $password = filter_input(INPUT_POST, "Password")??"";
 $password = strip_tags($password);
 if ($password !== PHP_STR) {	
   $hash = hash("sha256", $password . APP_SALT, false);

   if (defined("APP_" . strtoupper(AVATAR_NAME) . "_HASH")) {
      if ($hash !== constant("APP_" . strtoupper(AVATAR_NAME) . "_HASH")) {
        $password=PHP_STR;	
      }	 
   } else {
      if ($hash !== APP_HASH) {
        $password=PHP_STR;	
      }	 
   }   
   
//  if ($hash !== APP_HASH) {
//    $password=PHP_STR;	
//  }	 
   
 } 
 if ($password !== PHP_STR) {
   $CURRENT_VIEW = ADMIN_VIEW;
 } else {
   $CURRENT_VIEW = PUBLIC_VIEW;
 } 

 $magicJar1 = (int)substr(strip_tags(filter_input(INPUT_POST, "txtMagicJar1")??""), 0, 1);
 $magicJar2 = (int)substr(strip_tags(filter_input(INPUT_POST, "txtMagicJar2")??""), 0, 1);
 $magicJar3 = (int)substr(strip_tags(filter_input(INPUT_POST, "txtMagicJar3")??""), 0, 1);

 if ($CURRENT_VIEW === PUBLIC_VIEW ) {
     $MAXP = (int)substr(strip_tags(filter_input(INPUT_GET, "maxp")??""), 0, 2);
     if ($MAXP === 0) {
       $MAXP = APP_BLOG_WIDE_MAX_POSTS;
     }  
 }
 
 function uploadNewRes() {

   global $AVATAR_PATH;
   global $CV_PATH;      
   global $FRIENDS_PATH;      
   global $BLOG_PATH;      
   global $GALLERY_PATH;      
   global $MAGICJAR1_PATH;      
   global $MAGICJAR2_PATH;
   global $MAGICJAR3_PATH;
   global $magicJar1;
   global $magicJar2;
   global $magicJar3;

   //echo_ifdebug(true, "AVATAR_PATH#1=");
   //echo_ifdebug(true, $AVATAR_PATH);
   
   if (!empty($_FILES['files']['tmp_name'][0]) ||  !empty($_FILES['filesdd']['tmp_name'][0])) {
      
     $uploads = (array)fixMultipleFileUpload($_FILES['files']);
     if ($uploads[0]['error'] === PHP_UPLOAD_ERR_NO_FILE) {
       $uploads = (array)fixMultipleFileUpload($_FILES['filesdd']);
     }   
     
     //if ($uploads[0]['error'] === PHP_UPLOAD_ERR_NO_FILE) {
     //  echo("WARNING: No file uploaded.");
     //  return;
     //} 

     $google = "abcdefghijklmnopqrstuvwxyz";
     if (count($uploads)>strlen($google)) {
       echo("WARNING: Too many uploaded files."); 
       return;
     }

     $i=1;
     foreach($uploads as &$upload) {
		
       switch ($upload['error']) {
       case PHP_UPLOAD_ERR_OK:
         break;
       case PHP_UPLOAD_ERR_NO_FILE:
         echo("WARNING: One or more uploaded files are missing.");
         return;
       case PHP_UPLOAD_ERR_INI_SIZE:
         echo("WARNING: File exceeded INI size limit.");
         return;
       case PHP_UPLOAD_ERR_FORM_SIZE:
         echo("WARNING: File exceeded form size limit.");
         return;
       case PHP_UPLOAD_ERR_PARTIAL:
         echo("WARNING: File only partially uploaded.");
         return;
       case PHP_UPLOAD_ERR_NO_TMP_DIR:
         echo("WARNING: TMP dir doesn't exist.");
         return;
       case PHP_UPLOAD_ERR_CANT_WRITE:
         echo("WARNING: Failed to write to the disk.");
         return;
       case PHP_UPLOAD_ERR_EXTENSION:
         echo("WARNING: A PHP extension stopped the file upload.");
         return;
       default:
         echo("WARNING: Unexpected error happened.");
         return;
       }
      
       if (!is_uploaded_file($upload['tmp_name'])) {
         echo("WARNING: One or more file have not been uploaded.");
         return;
       }
      
       // name	 
       $name = (string)substr((string)filter_var($upload['name']), 0, 255);
       if ($name == PHP_STR) {
         echo("WARNING: Invalid file name: " . $name);
         return;
       } 
       $upload['name'] = $name;
       
       // fileType
       $fileType = substr((string)filter_var($upload['type']), 0, 30);
       $upload['type'] = $fileType;	 
       
       // tmp_name
       $tmp_name = substr((string)filter_var($upload['tmp_name']), 0, 300);
       if ($tmp_name == PHP_STR || !file_exists($tmp_name)) {
         echo("WARNING: Invalid file temp path: " . $tmp_name);
         return;
       } 
       $upload['tmp_name'] = $tmp_name;
       
       //size
       $size = substr((string)filter_var($upload['size'], FILTER_SANITIZE_NUMBER_INT), 0, 12);
       if ($size == "") {
         echo("WARNING: Invalid file size.");
         return;
       } 
       $upload["size"] = $size;

       $tmpFullPath = $upload["tmp_name"];
       
       $originalFilename = pathinfo($name, PATHINFO_FILENAME);
       $originalFileExt = pathinfo($name, PATHINFO_EXTENSION);
       $fileExt = strtolower(pathinfo($name, PATHINFO_EXTENSION));

       $date = date("Ymd-His");
       $rnd = mt_rand(1000000000, 9999999999);    
       
       if ($originalFileExt!==PHP_STR) {
         $destFileName = $date . "-" . $rnd . substr($google, $i-1, 1) . "|" . str_replace(" ", "_", $originalFilename) . ".$fileExt";
       } else {
         return; 
       }	   

       //$CV_PATH = APP_DATA_PATH . DIRECTORY_SEPARATOR . "cv";      
       //$FRIENDS_PATH = APP_DATA_PATH . DIRECTORY_SEPARATOR . "friends";      
       //$BLOG_PATH = APP_DATA_PATH . DIRECTORY_SEPARATOR . "blog";      
       //$GALLERY_PATH = APP_DATA_PATH . DIRECTORY_SEPARATOR . "gallery";      
       
       $destPaths = [];
       $destFullPaths = [];
       
       if ($magicJar1 != 0) {
         $destPaths[] = $MAGICJAR1_PATH;
         $destFullPaths[] = $destPaths[count($destPaths)-1] . DIRECTORY_SEPARATOR . $destFileName;
       }
       if ($magicJar2 != 0) {
         $destPaths[] = $MAGICJAR2_PATH;
         $destFullPaths[] = $destPaths[count($destPaths)-1] . DIRECTORY_SEPARATOR . $destFileName;
       }
       if ($magicJar3 != 0) {
         $destPaths[] = $MAGICJAR3_PATH;
         $destFullPaths[] = $destPaths[count($destPaths)-1] . DIRECTORY_SEPARATOR . $destFileName;
       }
       
       if (empty($destPaths)) {
       
          switch ($fileExt) {
            case "doc":
            case "docx":
            case "pdf":
              $destPaths[] = $CV_PATH;
              break;
            //case "txt":
            //  $destPaths[] = $BLOG_PATH;
            //  break;
            case "png":
            case "jpg":
            case "jpeg":
            case "gif":
            case "webp":
              $destPaths[] = $BLOG_PATH; 
              break;
            default:
              $destPaths[] = $MAGICJAR1_PATH;
              break;
          }
          $destFullPaths[] = $destPaths[0] . DIRECTORY_SEPARATOR . $destFileName;
       }     
       
       $iPath = 0;
       foreach($destFullPaths as $destFullPath) {
       
          if (file_exists($destFullPath)) {
            echo("WARNING: destination already exists");
            exit(1);
          }	   

          if (filesize($tmpFullPath) > APP_FILE_MAX_SIZE) {
            echo("ERROR: file size(" . filesize($tmpFullPath) . ") exceeds app limit:" . APP_FILE_MAX_SIZE);
            exit(1);
          }
          
          if (!is_readable($AVATAR_PATH)) {
            mkdir($AVATAR_PATH, 0777); 
          }

          if (!is_readable($destPaths[$iPath])) {
            mkdir($destPaths[$iPath], 0777); 
          }

          $pattern = $destPaths[$iPath] . DIRECTORY_SEPARATOR . "*" . "|" . str_replace(" ", "_", $originalFilename) . ".$fileExt";
          $aExistingPaths = glob($pattern);
          if (!empty($aExistingPaths)) {
            continue;
          }

          copy($tmpFullPath, $destFullPath);

          $iPath++;
       }   
          
       // Cleaning up..
      
       // Delete the tmp file..
       unlink($tmpFullPath); 
       
       $i++;
        
     }	 
   } else {
     //echo("WARNING: No file uploaded (err-pip-po).");
   }
 }

 function writeFriends() {
   
   global $FRIENDS_PATH; 
   
   $destPath = $FRIENDS_PATH;
   
   $s = filter_input(INPUT_POST, "f")??"";
   $s = strip_tags($s);
   if ($s != PHP_STR) {
   //echo($s);
   //exit(0);
     $friends=explode("|", $s);
     
     if (!is_readable($destPath)) {
       mkdir($destPath, 0777); 
     }
     
     foreach($friends as $friend) {
       $a = explode("://",$friend);
       $s = $a[1];
       $a = explode("/", $s); 
       $friendName = $a[0] . ".txt";
       
       file_put_contents($destPath . DIRECTORY_SEPARATOR . $friendName, $friend);
     }
     
   }  
 }
 
 function grabProfileImage() {
   
   global $BLOG_PATH;
   
   $pattern = $BLOG_PATH . DIRECTORY_SEPARATOR . "*";
   $aImagePaths = glob($pattern);
   if (isset($aImagePaths[0])) {
     $retval = basename($aImagePaths[0]);
   } else {
     $retval = null;
   }
   return $retval;
   
 }
 
 function startApp() {
   global $CURRENT_VIEW;
   global $profilePic;
   
   if ($CURRENT_VIEW == ADMIN_VIEW) {
   
     uploadNewRes();
   
     writeFriends();
  
   }
     
   $profilePic = grabProfileImage() ?? APP_DEF_PROFILE_PIC;
   //echo("profile pic=" . $profilePic);
  
 }  
 startApp();
 
?>

<!DOCTYPE html>
<html lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
<head>

  <meta name="viewport" content="width=device-width, initial-scale=1"/>
   
<!--<?PHP echo(APP_LICENSE);?>-->  
  
  <title><?PHP echo(APP_TITLE);?></title>

  <link rel="shortcut icon" href="/favicon.ico" />

<?PHP if ($CURRENT_VIEW == PUBLIC_VIEW): ?> 
 <script>
 function renderCorrectPag () {
      if (window.innerWidth <= 500) {
         if (<?PHP echo($MAXP) ?>!=<?PHP echo(APP_BLOG_ULTRATHIN_MAX_POSTS) ?>) {
             window.open("/<?PHP echo(AVATAR_NAME); ?>/?maxp=<?PHP echo(APP_BLOG_ULTRATHIN_MAX_POSTS) ?>","_self");
          }   
      } else if (window.innerWidth <= 850) {
          if (<?PHP echo($MAXP) ?>!=<?PHP echo(APP_BLOG_THIN_MAX_POSTS) ?>) {
             window.open("/<?PHP echo(AVATAR_NAME); ?>/?maxp=<?PHP echo(APP_BLOG_THIN_MAX_POSTS) ?>","_self");
          }  
      } else {
          if (<?PHP echo($MAXP) ?>!=<?PHP echo(APP_BLOG_WIDE_MAX_POSTS) ?>) {
             window.open("/<?PHP echo(AVATAR_NAME); ?>/?maxp=<?PHP echo(APP_BLOG_WIDE_MAX_POSTS) ?>","_self");
          }  
      }
 }
 
  window.addEventListener("load", function() {
     renderCorrectPag();
  }, false);
  
  window.addEventListener("resize", function() {
     renderCorrectPag();
  }, false);
 </script>
<?PHP endif; ?>

  <meta name="description" content="Welcome to Puzzleu! Let everyone have its puzzle."/>
  <meta name="keywords" content="puzzleu,photo,puzzle,on,premise,solution"/>
  <meta name="robots" content="index,follow"/>
  <meta name="author" content="5 Mode"/>
  
  <script src="/js/jquery-3.6.0.min.js" type="text/javascript"></script>
  <script src="/js/sha.js" type="text/javascript"></script>
  <script src="/js/common.js" type="text/javascript"></script>
  <script src="/js/bootstrap.min.js" type="text/javascript"></script>  
    
  <link href="/css/style.css?r=<?PHP echo(time());?>" type="text/css" rel="stylesheet">
  
  <link href="/css/bootstrap.min.css" type="text/css" rel="stylesheet">
   
</head>
  
<?PHP if ($CURRENT_VIEW == ADMIN_VIEW): ?>    
  
  <body style="background:url('/res/bg1.jpg') no-repeat; background-size: cover; background-attachment: fixed; background-position: center;">
   <div id="AFHint" style="z-index:0;">
        <button type="button" class="close" aria-label="Close" onclick="closeMe(this);" style="position:relative; top:5px; left:-7px;">
           <span aria-hidden="true" style="color:black; font-weight:900;">&times;</span>
       </button>
       <br>  
      <span onclick="showHowTo();"><?PHP echo(getResource0("How-to: Manage your avatars in Puzzleu", $lang));?></span>
      <br><br>
   </div>     

  <form id="frmUpload" role="form" method="post" action="/<?PHP echo(AVATAR_NAME);?>?hl=<?PHP echo($lang);?>" target="_self" enctype="multipart/form-data">  
    
  <div class="dragover" dropzone="copy">  
  
   <div id="fireupload" onclick="$('#files').click()">
       <img id="picavatar" src="/img?av=<?PHP echo(AVATAR_NAME);?>&pic=<?PHP echo($profilePic);?>" align="middle">  
   </div> 
    
    <input id="files" name="files[]" type="file" accept=".*" style="visibility: hidden;" multiple>    
  
    <input type="hidden" id="a" name="a">    
    <input type="hidden" id="f" name="f">  
    
  </div>  

<div class="tools">
<div class="settingson" onclick="settingsOn();"></div>

  <?PHP if ($magicJar1 == 0): ?>
<div class="magicjar1" style="background:url(/res/magicjar1dis.png);" onclick="setJar1On()"></div>
<?PHP else: ?>
<div class="magicjar1" style="background:url(/res/magicjar1.png);" onclick="setJar1Off()"></div>
<?PHP endif; ?>

<?PHP if ($magicJar2 == 0): ?>
<div class="magicjar2" style="background:url(/res/magicjar2dis.png);" onclick="setJar2On()"></div>
<?PHP else: ?>
<div class="magicjar2" style="background:url(/res/magicjar2.png);" onclick="setJar2Off()"></div>
<?PHP endif; ?>

<?PHP if ($magicJar3 == 0): ?>
<div class="magicjar3" style="background:url(/res/magicjar3dis.png);" onclick="setJar3On()"></div>
<?PHP else: ?>
<div class="magicjar3" style="background:url(/res/magicjar3.png);" onclick="setJar3Off()"></div>
<?PHP endif; ?>

<div class="settingsoff" onclick="settingsOff();"></div>
</div>

<input type="hidden" id="txtMagicJar1" name="txtMagicJar1" value="<?PHP echo($magicJar1);?>">
<input type="hidden" id="txtMagicJar2" name="txtMagicJar2" value="<?PHP echo($magicJar2);?>">
<input type="hidden" id="txtMagicJar3" name="txtMagicJar3" value="<?PHP echo($magicJar3);?>">
    
 <input type="hidden" id="Password" name="Password" value="<?PHP echo($password);?>"> 
    
 </form>   
           
  <div id="footerCont">&nbsp;</div>
  <div id="footer"><span style="background:#FFFFFF; opacity:0.7;">&nbsp;&nbsp;<a class="aaa" href="dd.html">Disclaimer</a>.&nbsp;&nbsp;A <a href="http://5mode.com" class="aaa">5 Mode</a> project and <a href="http://demo.5mode.com" class="aaa">WYSIWYG</a> system. <?PHP echo(getResource0("Some rights reserved", $lang));?></span></div>
           
<?PHP else: ?>          

  <body style="background:#dadada no-repeat; background-size: cover; background-attachment: fixed; background-position: center;">
   <!--<div id="AFHint">
        <button type="button" class="close" aria-label="Close" onclick="closeMe(this);" style="position:relative; top:5px; left:-7px;">
           <span aria-hidden="true" style="color:black; font-weight:900;">&times;</span>
       </button>
       <br>  
      <span onclick="showHowTo();"><?PHP echo(getResource0("How-to: Manage your avatars in Puzzleu", $lang));?></span>
      <br><br>
   </div> -->
   
   <br>
   
   <div id="header" class="header" style="margin-top:18px;margin-bottom:18px;">
        <div style="float:left">
             <a href="http://puzzleu.5mode-foss.eu" target="_self" style="color:#000000; text-decoration: none;">&nbsp;<img src="/res/AFlogo.png" align="middle" style="position:relative;top:-5px;width:22px;">&nbsp;Puzzleu</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="https://github.com/par7133/Puzzleu" style="color:#000000;"><span style="color:#119fe2">on</span> github</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="mailto:posta@elettronica.lol" style="color:#000000;"><span style="color:#119fe2">for</span> feedback</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="tel:+39-331-4029415" style="font-size:13px;background-color:#15c60b;border:2px solid #15c60b;color:#000000;height:27px;text-decoration:none;">&nbsp;&nbsp;get support&nbsp;&nbsp;</a>
        </div>
        <div style="float:right; position:relative; top:-10px;margin-right:300px;">
              <a href="#" onclick="slideShow();"><img src="/res/playicon.png" style="width:45px;"></a>
        </div>     
   </div>
   <div id="headerMob" class="header" style="margin-top:18px;margin-bottom:18px;display:none">
        <div style="float:left">
             <a href="http://puzzleu.5mode-foss.eu" target="_self" style="color:#000000; text-decoration: none;">&nbsp;<img src="/res/AFlogo.png" align="middle" style="position:relative;top:-5px;width:22px;">&nbsp;Puzzleu</a>
        </div>   
   </div>
   
   <br>  
         
   <form id="frmUpload" role="form" method="post" action="/<?PHP echo(AVATAR_NAME);?>?hl=<?PHP echo($lang);?>" target="_self" enctype="multipart/form-data">  
      
 <div id="blog">    
      <?PHP
   $iEntry = 2;   
   $totLinks = 0;
   $aLinks=[];
   $pattern = $BLOG_PATH . DIRECTORY_SEPARATOR . "*";
   $aFilePaths = glob($pattern);   
   if (empty($aFilePaths)): ?>
            <div class="blog-content"> 
              <div class="blog-entry" style="background:url('/res/img.png');background-size:100% 100%;">    
                &nbsp;
              </div> 
             </div>  
        <?PHP else: ?>
                <?PHP
      $CUDOZ++;          
      $iEntry = 1;          
      $iCurEntry = 1; 
      arsort($aFilePaths, SORT_STRING);
      $totPost = count($aFilePaths);
      
      // PAGINATION

      $totPages = (int)(count($aFilePaths)/$MAXP); 
      if ($totPages < (count($aFilePaths)/$MAXP)) {
        $totPages++;
      }
      $firstPost = 0;
      $prevPost = $blogSP - $MAXP;
      if ($prevPost < 0) {
        $prevPost = 0;
      }    
      $nextPost = $blogSP + $MAXP;
      if ($nextPost > (($totPages - 1) * $MAXP)) {
        $nextPost = (($totPages - 1) * $MAXP);
      }
      if ($nextPost < 0) {
        $nextPost = 0;
      }       
      $lastPost = (($totPages - 1) * $MAXP);
      if ($lastPost < 0) {
        $lastPost = 0;
      }    
      // ---      
      
      //echo("blogSP=".$blogSP);
      foreach ($aFilePaths as $filePath) {
        //echo("iCurEntry=".$iCurEntry);
        if ($iCurEntry<($blogSP+1)) {
          $iCurEntry++;
          continue;
        }  
        if (($iEntry>$MAXP) || (!APP_PAGINATION && $iCurEntry>APP_BLOG_MAX_POSTS)) {
          break;
        }
        $orifilename = basename($filePath);
        $orifileExt = strtolower(pathinfo($orifilename, PATHINFO_EXTENSION));
        $date = explode("-",$orifilename)[0];
        $time = explode("-",$orifilename)[1];
        $time = left($time,2) . ":" . substr($time,2,2);
        if ($iEntry === count($aFilePaths) || $iEntry==$MAXP) {
          $marginbottom = "0px";
        } else {
          $marginbottom = "5px";
        }
        ?>
                     <?PHP if (in_array($orifileExt, ["png", "jpg", "jpeg", "gif", "webp"])):?>      
                          <div class="blog-content"> 
                           <div class="blog-entry" onclick="selectVideo(<?php echo($iEntry-1);?>);" style="background:url('/img?av=<?PHP echo(AVATAR_NAME);?>&pic=<?PHP echo($orifilename);?>');background-size:100% 100%;">  
                                <?PHP if (APP_PAGINATION || (APP_BLOG_MAX_POSTS===0 || APP_BLOG_MAX_POSTS>$iCurEntry)): ?>
                                      <?php if ($iCurEntry===1 && $iEntry===1): ?>
                                       <!--<img class="blog-img" src="/res/arrow-leftd.png" style="float:left;">-->
                                       <?php elseif ($iEntry===1 && $iCurEntry>1): ?>  
                                       <a href="/<?PHP echo(AVATAR_NAME); ?>/?blogSP=<?PHP echo($prevPost);?>&maxp=<?PHP echo($MAXP)?>" onclick="event.stopPropagation();"><img class="blog-img" src="/res/arrow-left.png" style="float:left;"></a>
                                       <?php endif; ?>
                                       <?php if ($iEntry===$MAXP && $iCurEntry===$totPost): ?>
                                       <!--<img class="blog-img" src="/res/arrow-rightd.png" style="float:right;">-->
                                       <?php elseif ($iEntry===$MAXP): ?>
                                          <?PHP if ($MAXP===APP_BLOG_ULTRATHIN_MAX_POSTS): ?> 
                                            <div style="float:right;position: absolute;left:+65%; opacity:0.85;"><a href="/<?PHP echo(AVATAR_NAME); ?>/?blogSP=<?PHP echo($nextPost);?>&maxp=<?PHP echo($MAXP)?>" onclick="event.stopPropagation();"><img class="blog-img" src="/res/arrow-right.png" style="float:right;"></a></div>
                                          <?PHP elseif ($MAXP===APP_BLOG_THIN_MAX_POSTS): ?>   
                                            <div style="float:right;position: absolute;left:+82%; opacity:0.85;"><a href="/<?PHP echo(AVATAR_NAME); ?>/?blogSP=<?PHP echo($nextPost);?>&maxp=<?PHP echo($MAXP)?>" onclick="event.stopPropagation();"><img class="blog-img" src="/res/arrow-right.png" style="float:right;"></a></div>                                          
                                          <?PHP else: ?>                                            
                                            <div style="float:right;position: absolute;left:+92%; opacity:0.85;"><a href="/<?PHP echo(AVATAR_NAME); ?>/?blogSP=<?PHP echo($nextPost);?>&maxp=<?PHP echo($MAXP)?>" onclick="event.stopPropagation();"><img class="blog-img" src="/res/arrow-right.png" style="float:right;"></a></div>
                                          <?php endif; ?>  
                                       <?php endif; ?>
                                 <?PHP else: ?>
                                       &nbsp;
                                 <?PHP endif; ?>      
                           </div> 
                          </div>   
                          <?PHP $aLinks[] = "/img?av=" . AVATAR_NAME . "&pic=" . $orifilename; ?>
                      <?PHP EndIf; ?>
                 <?PHP 
       $totLinks = $iEntry;          
       $iEntry++;          
       $iCurEntry++;
      }?>
   
      <?PHP endif; ?>

    <?PHP for($i=$iEntry;$i<=$MAXP;$i++):?>
            <div class="blog-content"> 
             <div class="blog-entry" style="border:0px;background:darkgray;">  
                 &nbsp;
             </div> 
            </div>   
   <?PHP endfor; ?>
   
   <?PHP if (($MAXP / 3) > (int)($MAXP / 3)): ?>
            <div class="blog-content"> 
             <div class="blog-entry" style="border:0px;background:darkgray;">  
                 &nbsp;
             </div> 
            </div>   
   
           <?PHP if ((($MAXP+1) / 3) > (int)(($MAXP+1) / 3)): ?>
                 <div class="blog-content"> 
                  <div class="blog-entry" style="border:0px;background:darkgray;">  
                      &nbsp;
                  </div> 
                 </div>   
        <?PHP endif; ?>
   
   <?PHP endif; ?>
   <br><br>
   
   </div> 

  <?php
      $iLink = 0;  
      foreach($aLinks as $aLink) { ?>

                    <button id="modalButton<?php echo($iLink);?>" type="button" class="btn btn-primary" style="display:none;" data-toggle="modal" data-target="#modal<?php echo($iLink);?>">Button #<?php echo($iLink);?></button>

                    <div class="modal" tabindex="-1" role="dialog" id="modal<?php echo($iLink);?>" style="z-index:99997">
                      <div class="modal-dialog modal-lg" role="document" style="width:95%;margin-top:0;background-color:#FFFFFF;z-index:99998">
                        <div class="modal-content" style="float:left;width: content-box;max-width:60%;z-index:99999;">
 
                          <img id="imageh<?PHP echo($iLink);?>" class="imageh" src="<?php echo($aLink); ?>" style="display:none">
                          
                          <img id="image<?PHP echo($iLink);?>" class="image" src="" marti-src="<?php echo($aLink); ?>" style="width:100%;vertical-align:middle;">

                        </div>
                        <!--
                        <div class="modal-toolbox" style="float:right;">
                          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div> --> 
                      </div>  
                    </div> 

      <?php $iLink++; ?>
  
 <?php } ?>
  
   <?PHP 
   // GALLERY GHOST
   $CUDOZ++;
 ?>          
        
  <div id="halSys" class="col-xs-12 col-sm-10 col-md-10 col-lg-7 col-xl-5 col-haligned input-group" style="display: none; position:relative; top:-85px; left:-95px;">
 
   <div class="input-group-btn" style="border: 0px solid red;">
     <span id="halTerm" style="float: left; position:relative; left:+4%; top: +2px; cursor:pointer;"><img id="hal" src="/res/hal2_closed.png" style="width: 52px; position:relative; left:+5px;"></span>
     <div id="halBoard" style="float: left; position:relative; left:+5%; top: +2px; display: none;">

      <?PHP
    $pattern = $CV_PATH . DIRECTORY_SEPARATOR . "*";
    $aFilePaths = glob($pattern);
    if (!empty($aFilePaths)): ?>
               <?PHP $CUDOZ++; ?>
          
               <h3 class="board-entry" style="font-size: 1.45vw; float:left; color:#000000; background-color:lightgray; opacity:0.7; margin-right:4px; padding:4px; margin-left:3px; margin-right:3px;height: 24px;">

                <nobr>
                 <div class="input-group">

                    <div class="input-group-btn btn-white dropup">
                      <a id="halCVOptions" class="btn dropdown-toggle btn-link btn-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 0.95vw; font-weight:700; top:-9px; cursor:pointer; color: #000000;"><?php echo("CV");?></a>
                      <table class="dropdown-menu cv-options-table bubble" style="background-color: white; left:-6px; margin-bottom: 10px; z-index:99999;">
                      <tr>
                        <td class="cv-options-td">
                          <br>
                      <?PHP                      
         $pattern = $CV_PATH . DIRECTORY_SEPARATOR . "*.doc";
         $aFilePaths = glob($pattern);
         if (!empty($aFilePaths)): ?>
                            
                           <a id="halCVDoc" href="/doc?av=<?PHP echo(AVATAR_NAME);?>&re=cv&doc=<?PHP echo(basename($aFilePaths[0]));?>" style="cursor:pointer; color: #000000; font-weight:700;"><?php echo("doc");?></a><br><br>
                               
                        <?PHP endif; ?>
                               
                      <?PHP                      
         $pattern = $CV_PATH . DIRECTORY_SEPARATOR . "*.pdf";
         $aFilePaths = glob($pattern);
         if (!empty($aFilePaths)): ?>
                           <a id="halCVPdf" href="/doc?av=<?PHP echo(AVATAR_NAME);?>&re=cv&doc=<?PHP echo(basename($aFilePaths[0]));?>" style="cursor:pointer; color: #000000; font-weight:700;"><?php echo("pdf");?></a><br> <br>
                        
                         <?PHP endif; ?>   
                               
                        </td>
                      </tr>      
                      </table>
                    </div>

                </div>
                </nobr>

              &nbsp;&nbsp; &nbsp;</h3>
     <?PHP endif; ?>
          
     <?PHP
   $pattern = $MAGICJAR1_PATH . DIRECTORY_SEPARATOR . "*";
   $aFilePaths = glob($pattern);
   
   if (!empty($aFilePaths)): ?>
       
         <h3 class="board-entry" style="font-size: 1.45vw; float:left; color:#000000; background-color:lightgray; opacity:0.7; margin-right:4px; padding:4px; margin-left:3px; margin-right:3px;height: 24px;">

         <nobr>
          <div class="input-group">

             <div class="input-group-btn btn-white dropup">
               <a id="halMP1Options" class="btn dropdown-toggle btn-link btn-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 0.95vw; font-weight:700; top:-9px; cursor:pointer; color: #000000;">MagicPot1</a>
               <table class="dropdown-menu cv-options-table bubble" style="background-color: white; left:-6px; margin-bottom: 10px; z-index:99999;">
               <tr>
                 <td class="cv-options-td">
                   <br>
                     
                    <?PHP
          $iEntry = 1;          
          foreach ($aFilePaths as $filePath) {
            $orifilename = basename($filePath);
            $orifileExt = strtolower(pathinfo($orifilename, PATHINFO_EXTENSION));
            $filename = explode("|",basename($filePath))[1];
            if ($iEntry === count($aFilePaths)) {
              $marginbottom = "0px";
            } else {
              $marginbottom = "5px";
            }
            ?>
                            <?PHP if (in_array($orifileExt, ["png", "jpg", "jpeg", "gif", "webp"])):?>                   
                            <a id="halMP1Doc<?PHP echo($iEntry);?>" href="/imgj?av=<?PHP echo(AVATAR_NAME);?>&jar=1&fn=<?PHP echo($orifilename);?>" style="cursor:pointer; color: #000000; font-weight:700;margin-right:10px;">
                            <?PHP else: ?>  
                            <a id="halMP1Doc<?PHP echo($iEntry);?>" href="/file?av=<?PHP echo(AVATAR_NAME);?>&jar=1&fn=<?PHP echo($orifilename);?>" style="cursor:pointer; color: #000000; font-weight:700;margin-right:10px;">
                            <?PHP endif; ?>  
                            <?PHP echo($filename);?>
                            </a>
                            <?PHP if ($iEntry !== count($aFilePaths)): ?>  
                            <br><br>
                            <?PHP else: ?>    
                             <br>   
                            <?PHP endif; ?>    
                     <?PHP 
           $iEntry++;          
          }?>

                     <br>
                 </td>
               </tr>      
               </table>
             </div>

          </div>
        </nobr>

       &nbsp;&nbsp;&nbsp;</h3>
      
        <?PHP endif; ?>

     <?PHP
   $pattern = $MAGICJAR2_PATH . DIRECTORY_SEPARATOR . "*";
   $aFilePaths = glob($pattern);
   
   if (!empty($aFilePaths)): ?>
      <!--<h3 class="board-entry" style="border-radius:3px;font-size: 1.45vw; font-weight:700; float:left; background-color:lightgray; opacity:0.7; margin-right:4px; padding:4px;">
            &nbsp;&nbsp;<a id="halMagicpot1" onclick="#" style="cursor:pointer; color: #000000;">Magicpot1</a>&nbsp;&nbsp;
      </h3>-->
         <h3 class="board-entry" style="font-size: 1.45vw; float:left; color:#000000; background-color:lightgray; opacity:0.7; margin-right:4px; padding:4px; margin-left:3px; margin-right:3px;height: 24px;">

         <nobr>
          <div class="input-group">

             <div class="input-group-btn btn-white dropup">
               <a id="halMP1Options" class="btn dropdown-toggle btn-link btn-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 0.95vw; font-weight:700; top:-9px; cursor:pointer; color: #000000;">MagicPot2</a>
               <table class="dropdown-menu cv-options-table bubble" style="background-color: white; left:-6px; margin-bottom: 10px; z-index:99999;">
               <tr>
                 <td class="cv-options-td">
                   <br>
                     
                    <?PHP
          $iEntry = 1;          
          foreach ($aFilePaths as $filePath) {
            $orifilename = basename($filePath);
            $orifileExt = strtolower(pathinfo($orifilename, PATHINFO_EXTENSION));
            $filename = explode("|",basename($filePath))[1];
            if ($iEntry === count($aFilePaths)) {
              $marginbottom = "0px";
            } else {
              $marginbottom = "5px";
            }
            ?>
                            <?PHP if (in_array($orifileExt, ["png", "jpg", "jpeg", "gif", "webp"])):?>                   
                            <a id="halMP1Doc<?PHP echo($iEntry);?>" href="/imgj?av=<?PHP echo(AVATAR_NAME);?>&jar=2&fn=<?PHP echo($orifilename);?>" style="cursor:pointer; color: #000000; font-weight:700;margin-right:10px;">
                            <?PHP else: ?>  
                            <a id="halMP1Doc<?PHP echo($iEntry);?>" href="/file?av=<?PHP echo(AVATAR_NAME);?>&jar=2&fn=<?PHP echo($orifilename);?>" style="cursor:pointer; color: #000000; font-weight:700;margin-right:10px;">
                            <?PHP endif; ?>  
                            <?PHP echo($filename);?>
                            </a>
                            <?PHP if ($iEntry !== count($aFilePaths)): ?>  
                            <br><br>
                            <?PHP else: ?>    
                             <br>   
                            <?PHP endif; ?>    
                     <?PHP 
           $iEntry++;          
          }?>

                     <br>
                 </td>
               </tr>      
               </table>
             </div>

          </div>
        </nobr>

       &nbsp;&nbsp;&nbsp;</h3>
      
        <?PHP endif; ?>

     <?PHP
   $pattern = $MAGICJAR3_PATH . DIRECTORY_SEPARATOR . "*";
   $aFilePaths = glob($pattern);
   
   if (!empty($aFilePaths)): ?>
      <!--<h3 class="board-entry" style="border-radius:3px;font-size: 1.45vw; font-weight:700; float:left; background-color:lightgray; opacity:0.7; margin-right:4px; padding:4px;">
            &nbsp;&nbsp;<a id="halMagicpot1" onclick="#" style="cursor:pointer; color: #000000;">Magicpot1</a>&nbsp;&nbsp;
      </h3>-->
         <h3 class="board-entry" style="font-size: 1.45vw; float:left; color:#000000; background-color:lightgray; opacity:0.7; margin-right:4px; padding:4px; margin-left:3px; margin-right:3px;height: 24px;">

         <nobr>
          <div class="input-group">

             <div class="input-group-btn btn-white dropup">
               <a id="halMP1Options" class="btn dropdown-toggle btn-link btn-white" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="font-size: 0.95vw; font-weight:700; top:-9px; cursor:pointer; color: #000000;">MagicPot3</a>
               <table class="dropdown-menu cv-options-table bubble" style="background-color: white; left:-6px; margin-bottom: 10px; z-index:99999;">
               <tr>
                 <td class="cv-options-td">
                   <br>
                     
                    <?PHP
          $iEntry = 1;          
          foreach ($aFilePaths as $filePath) {
            $orifilename = basename($filePath);
            $orifileExt = strtolower(pathinfo($orifilename, PATHINFO_EXTENSION));
            $filename = explode("|",basename($filePath))[1];
            if ($iEntry === count($aFilePaths)) {
              $marginbottom = "0px";
            } else {
              $marginbottom = "5px";
            }
            ?>
                            <?PHP if (in_array($orifileExt, ["png", "jpg", "jpeg", "gif", "webp"])):?>                   
                            <a id="halMP1Doc<?PHP echo($iEntry);?>" href="/imgj?av=<?PHP echo(AVATAR_NAME);?>&jar=3&fn=<?PHP echo($orifilename);?>" style="cursor:pointer; color: #000000; font-weight:700;margin-right:10px;">
                            <?PHP else: ?>  
                            <a id="halMP1Doc<?PHP echo($iEntry);?>" href="/file?av=<?PHP echo(AVATAR_NAME);?>&jar=3&fn=<?PHP echo($orifilename);?>" style="cursor:pointer; color: #000000; font-weight:700;margin-right:10px;">
                            <?PHP endif; ?>  
                            <?PHP echo($filename);?>
                            </a>
                            <?PHP if ($iEntry !== count($aFilePaths)): ?>  
                            <br><br>
                            <?PHP else: ?>    
                             <br>   
                            <?PHP endif; ?>    
                     <?PHP 
           $iEntry++;          
          }?>

                     <br>
                 </td>
               </tr>      
               </table>
             </div>

          </div>
        </nobr>

       &nbsp;&nbsp;&nbsp;</h3>
      
      <?PHP endif; ?>      
     
      <?PHP
   $pattern = $FRIENDS_PATH . DIRECTORY_SEPARATOR . "*.txt";
   $aFilePaths = glob($pattern);
   if (!empty($aFilePaths)): ?>

                <?PHP
      $CUDOZ++;  
      $iEntry = 1;          
      foreach ($aFilePaths as $filePath) {
        $orifilename = basename($filePath);
        $link=file_get_contents($filePath);
        $filename = pathinfo($filePath, PATHINFO_FILENAME);
        if ($iEntry === count($aFilePaths)) {
          $marginbottom = "0px";
        } else {
          $marginbottom = "5px";
        }
        ?>
                  <h3 class="board-entry" style="border-radius:3px;font-size: 1.45vw; font-weight:700; float:left; background-color:lightgray; margin-right:4px; padding:4px;">
                        &nbsp;&nbsp;<a id="halFriend1" href="<?PHP echo($link);?>" style="cursor:pointer; color: #000000;"><?PHP echo($filename);?></a>&nbsp;&nbsp;
                  </h3>
                 <?PHP 
       $iEntry++;          
      }?>
      <?PHP endif; ?>
      
      <?PHP if (defined("APP_EMAIL_CONTACT") && APP_EMAIL_CONTACT!==PHP_STR): ?>
      <h3 class="board-entry" style="border-radius:3px;font-size: 1.45vw; font-weight:700; float:left; background-color:lightgray; margin-right:4px; padding:4px;">
        &nbsp;&nbsp;<a id="halContact" href="mailto:<?PHP echo(APP_EMAIL_CONTACT);?>" style="cursor:pointer; color: #000000;"><?PHP echo(APP_EMAIL_CONTACT);?></a>&nbsp;&nbsp;
      </h3>
      <?PHP Endif; ?>
      
     </div>
   </div>  

</div>
 
  <div id="passworddisplay">
       <br>  
        &nbsp;&nbsp;<input type="password" id="Password" name="Password" placeholder="password" value="<?php echo($password);?>" autocomplete="off">&nbsp;<input type="submit" value="<?PHP echo(getResource0("Go", $lang));?>" style="text-align:left;width:25%;color:#000000;"><br>
        &nbsp;&nbsp;<input type="text" id="Salt" placeholder="salt" autocomplete="off"><br>
        <div style="text-align:center;">
           <a id="hashMe" href="#" onclick="showEncodedPassword();"><?PHP echo(getResource0("Hash Me", $lang));?>!</a>
        </div>
 </div> 

 </form>       
     
  <div id="footerCont">&nbsp;</div>
  <div id="footer1" style="float:left; width:80px;">
        <select id="cbLang" onchange="changeLang(this);">
          <option value="en-US" <?PHP echo($lang==PHP_EN?"selected":"");?>>en</option>
            <option value="it-IT" <?PHP echo($lang==PHP_IT?"selected":"");?>>it</option>
            <option value="zh-CN" <?PHP echo($lang==PHP_CN?"selected":"");?>>cn</option>
        </select> 
    </div>
    <div id="footer2" style="float:right; width:450px;">
        <span style="background:#FFFFFF; opacity:0.7;">&nbsp;&nbsp;A <a href="http://5mode.com" class="aaa">5 Mode</a> project and <a href="http://demo.5mode.com" class="aaa">WYSIWYG</a> system. <?PHP echo(getResource0("Some rights reserved", $lang));?>.</span>
    </div>
           
 <?PHP endif; ?>           

<script src="/js/home-js.php?hl=<?PHP echo($lang);?>&av=<?PHP echo(AVATAR_NAME);?>&cv=<?PHP echo($CURRENT_VIEW);?>&cu=<?PHP echo($CUDOZ);?>" type="text/javascript"></script>

<?PHP if ($CURRENT_VIEW == PUBLIC_VIEW): ?> 
<script>
  function selectVideo(i) {
    //alert(3);
    y=0;
    $(".image").each(function(){
      if (y==i) {
        if ($("#imageh" + y).height() >= $("#imageh" + y).width()) {
          $(this).css("height", parseInt(window.innerHeight));
        } else {  
          $(this).css("height", "");
        }
        $(this).attr("src", $(this).attr("marti-src"));
      } else {
        $(this).attr("src", "");
      }
      y++;
    });
    //alert(i);
    //alert($('#iframe' + i).attr("id"));
    //$("#iframe"+i).attr("src", $("#iframe"+i).attr("marti-src") );
    $("#modalButton" + i).click();
  }
</script>  

<script>

  var bHal = false;

  $("span#halTerm").on("click",function(e) {
    bHal=!bHal;
    if (bHal) {
      document.getElementById("hal").src = '/res/hal2_open.png';
      $("div#halBoard").show("fast");
    } else {  
      document.getElementById("hal").src = '/res/hal2_closed.png';
      $("div#halBoard").hide("slow");
    }  
  });  

  function setHalPos() {
    bodyRect = document.body.getBoundingClientRect();
    //document.getElementById('q').write = bodyRect.width;
    bHal=false;
    document.getElementById("hal").src = '/res/hal2_closed.png';
    $("div#halBoard").hide();
    if (parseInt(bodyRect.width) < 690) {
      $("div#predSys").hide();
      $("div#halSys").hide();
    } else {
      $("div#predSys").show();
      $("div#halSys").show();
    }
    if (parseInt(bodyRect.width) > 1200) {
      $("h3.board-entry").css("font-size", "1.0vw");
      $("a#halCVOptions").css("font-size", "1.0vw");
      $("span#halTerm").css("left", "+11%");
      $("div#halBoard").css("left", "+12%");
    } else if (parseInt(bodyRect.width) < 800) {  
      $("h3.board-entry").css("font-size", "14px");
      $("a#halCVOptions").css("font-size", "14px");
      $("span#halTerm").css("left", "+0%");
      $("div#halBoard").css("left", "+0%");
    } else {
      $("span#halTerm").css("left", "+8%");
      $("div#halBoard").css("left", "+9%");
      $("h3.board-entry").css("font-size", "1.45vw");  
      $("a#halCVOptions").css("font-size", "1.45vw");
    }
  }  

  window.addEventListener("load", function() {
    setTimeout("setHalPos()", 100);
  }, true);  

  window.addEventListener("resize", function() {
    setTimeout("setHalPos()", 100);
  }, true);  

</script>

<script>

 function setContentPos() {
    h = window.innerHeight;
    w = window.innerWidth;
    pich = parseInt((h - $(".header").height() - 80) / 3);
    // picw = parseInt(w / 5); ori
    iimg = parseInt(<?PHP echo($MAXP / 3); ?>);
    if ((<?PHP echo($MAXP / 3); ?>) > parseInt(<?PHP echo($MAXP / 3); ?>)) {
      iimg++;
    }  
    picw = parseInt(w / iimg);
    $(".blog-content").css("height", pich + "px");
    $(".blog-content").css("width", picw + "px");
    $(".blog-entry").css("height", (pich-2) + "px");
    $(".blog-entry").css("width", (picw-2) + "px");
    $(".blog-img").css("height", (pich-4) + "px");   
    $(".blog-img").css("width", ((picw-4)/3) + "px");   
    // ---
    
    $("#passworddisplay").css("top", parseInt(h-$("#passworddisplay").height()-100)+"px");
 }

  window.addEventListener("load", function() {
    setTimeout("setContentPos()", 1000);
  });

  window.addEventListener("resize", function() {
    setTimeout("setContentPos()", 1000);
  });

</script>  

<script>
  
  var iSlide;
  var ii;
  
  function showSlide() {
    $(".modal").click();
    selectVideo(iSlide);
    if (iSlide<<?PHP echo($totLinks);?>) {
      iSlide++;
    } else {
      clearInterval(ii);
      $(".modal").click();
      
    }  
  }
  
  function slideShow() {
   iSlide = 0;
   ii = setInterval(showSlide,3700);
  }    
</script>  

<!-- SKINNER CODE -->
<?php if (is_readable($AVATAR_PATH . DIRECTORY_SEPARATOR . "skinner.html")): ?>
<?php include($AVATAR_PATH . DIRECTORY_SEPARATOR . "skinner.html"); ?> 
<?php else: ?>
      <?php if (file_exists(APP_PATH . DIRECTORY_SEPARATOR . "skinner.html")): ?>
      <?php include(APP_PATH . DIRECTORY_SEPARATOR . "skinner.html"); ?> 
      <?php endif; ?>
<?php endif; ?>

<!-- METRICS CODE -->
<?php if (file_exists(APP_PATH . DIRECTORY_SEPARATOR . "metrics.html")): ?>
<?php include(APP_PATH . DIRECTORY_SEPARATOR . "metrics.html"); ?> 
<?php endif; ?>

<?php endif; ?>

</body>
</html>
