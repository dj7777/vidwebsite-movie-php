<?php
if (!file_exists('../videos/configuration.php')) {
    if (!file_exists('../install/index.php')) {
        die("No Configuration and no Installation");
    }
    header("Location: install/index.php");
}

require_once '../videos/configuration.php';

require_once $global['systemRootPath'] . 'objects/user.php';
require_once $global['systemRootPath'] . 'objects/subscribe.php';
require_once $global['systemRootPath'] . 'objects/functions.php';

if (!empty($_GET['type'])) {
    if ($_GET['type'] == 'audio') {
        $_SESSION['type'] = 'audio';
    } else if ($_GET['type'] == 'video') {
        $_SESSION['type'] = 'video';
    } else {
        $_SESSION['type'] = "";
        unset($_SESSION['type']);
    }
}

require_once $global['systemRootPath'] . 'objects/video.php';
require_once $global['systemRootPath'] . 'objects/video_ad.php';
$catLink = "";
if (!empty($_GET['catName'])) {
    $catLink = "cat/{$_GET['catName']}/";
}
$video = Video::getVideo("", "viewableNotAd");
$obj = new Video("", "", $video['id']);
$resp = $obj->addView();
if (!empty($_GET['playlist_id'])) {
    $playlist_id = $_GET['playlist_id'];
    if (!empty($_GET['playlist_index'])) {
        $playlist_index = $_GET['playlist_index'];
    } else {
        $playlist_index = 0;
    }
    $videosPlayList = Video::getAllVideos("viewableNotAd");
    $video = Video::getVideo($videosPlayList[$playlist_index]['id']);
    if (!empty($videosPlayList[$playlist_index + 1])) {
        $autoPlayVideo = Video::getVideo($videosPlayList[$playlist_index + 1]['id']);
        $autoPlayVideo['url'] = $global['webSiteRootURL'] . "playlist/{$playlist_id}/" . ($playlist_index + 1);
    }
    unset($_GET['playlist_id']);
} else {
    $autoPlayVideo = Video::getRandom($video['id']);
    if (!empty($autoPlayVideo)) {
        $name2 = empty($autoPlayVideo['name']) ? substr($autoPlayVideo['user'], 0, 5) . "..." : $autoPlayVideo['name'];
        $autoPlayVideo['creator'] = '<div class="pull-left"><img src="' . User::getPhoto($autoPlayVideo['users_id']) . '" alt="" class="img img-responsive img-circle" style="max-width: 40px;"/></div><div class="commentDetails" style="margin-left:45px;"><div class="commenterName"><strong>' . $name2 . '</strong> <small>' . humanTiming(strtotime($autoPlayVideo['videoCreation'])) . '</small></div></div>';
        $autoPlayVideo['tags'] = Video::getTags($autoPlayVideo['id']);
        $autoPlayVideo['url'] = $global['webSiteRootURL'] . $catLink . "video/" . $autoPlayVideo['clean_title'];
    }
}

if (!empty($video)) {
    $ad = Video_ad::getAdFromCategory($video['categories_id']);
    $name = empty($video['name']) ? substr($video['user'], 0, 5) . "..." : $video['name'];
    $name = "<a href='{$global['webSiteRootURL']}channel/{$video['users_id']}/' class='btn btn-xs btn-default'>{$name}</a>";
    $subscribe = Subscribe::getButton($video['users_id']);

    $video['creator'] = '<div class="pull-left"><img src="' . User::getPhoto($video['users_id']) . '" alt="" class="img img-responsive img-circle" style="max-width: 40px;"/></div><div class="commentDetails" style="margin-left:45px;"><div class="commenterName text-muted"><strong>' . $name . '</strong><br>' . $subscribe . '<br><small>' . humanTiming(strtotime($video['videoCreation'])) . '</small></div></div>';
    $obj = new Video("", "", $video['id']);
    // dont need because have one embeded video on this page
    //$resp = $obj->addView();
}

if ($video['type'] !== "audio") {
    $poster = "{$global['webSiteRootURL']}images/{$video['postername']}.jpg";
} else {
  //  $poster = "{$global['webSiteRootURL']}view/img/audio_wave.jpg";
  $poster = "{$global['webSiteRootURL']}images/{$video['postername']}.jpg";
}

if (!empty($video)) {
    if ($video['type'] !== "audio") {
        $img = "{$global['webSiteRootURL']}images/{$video['postername']}.jpg";
    } else {
        $img = "{$global['webSiteRootURL']}view/img/audio_wave.jpg";
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['language']; ?>">
    <head>
        <title><?php echo $video['title']; ?> - <?php echo $config->getWebSiteTitle(); ?></title>
        <meta name="generator" content="Movief4u - A Free Movie website" />
        <?php
        include $global['systemRootPath'] . 'view/include/head.php';
        ?>
        <link rel="image_src" href="<?php echo $img; ?>" />
        <link href="<?php echo $global['webSiteRootURL']; ?>js/video.js/video-js.min.css" rel="stylesheet" type="text/css"/>
        <script src="<?php echo $global['webSiteRootURL']; ?>js/video.js/video.js" type="text/javascript"></script>
        <script src="<?php echo $global['webSiteRootURL']; ?>js/videojs-rotatezoom/videojs.zoomrotate.js" type="text/javascript"></script>
        <link href="<?php echo $global['webSiteRootURL']; ?>css/player.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $global['webSiteRootURL']; ?>css/social.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $global['webSiteRootURL']; ?>js/webui-popover/jquery.webui-popover.min.css" rel="stylesheet" type="text/css"/>
        <link href="<?php echo $global['webSiteRootURL']; ?>js/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css"/>
        <meta property="og:url"                content="<?php echo $global['webSiteRootURL'], $catLink, "video/", $video['clean_title']; ?>" />
        <meta property="og:type"               content="video" />
        <meta property="og:title"              content="<?php echo $video['title']; ?> - <?php echo $config->getWebSiteTitle(); ?>" />
        <meta property="og:description"        content="<?php echo $video['title']; ?>" />
        <meta property="og:image"              content="<?php echo $img; ?>" />
    </head>

    <body>
        <?php
        include 'include/navbar.php';
        ?>
        <div class="container-fluid principalContainer" itemscope itemtype="http://schema.org/VideoObject">
            <?php
            if (!empty($video)) {
                if (empty($video['type']) || file_exists("{$global['systemRootPath']}videos/{$video['filename']}.mp4")) {
                    $video['type'] = "video";
                }
                ?>
                <?php
                require "{$global['systemRootPath']}view/include/{$video['type']}.php";
                $img_portrait = ($video['rotation'] === "90" || $video['rotation'] === "270") ? "img-portrait" : "";
                ?>
                <div class="row">
                    <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1"></div>
                    <div class="col-xs-12 col-sm-11 col-md-11 col-lg-11 ">
                        <div class="row bgWhite list-group-item">
                            <div class="row divMainVideo">
                                <div class="col-xs-2 col-sm-2 col-lg-2">
                                    <img id="imgMainVideo" src="<?php echo $poster; ?>" alt="<?php echo $video['title']; ?>" class="img img-responsive <?php echo $img_portrait; ?> rotate<?php echo $video['rotation']; ?>" height="130px" itemprop="thumbnail" /> 
                                </div>

                                <div class="row col-xs-6 col-sm-6 col-lg-6">
                                <div class="mvic-desc">
                                    <h3>Movie4U</h3>

                                <div class="desc">
                                <h1 itemprop="name">
                                        <?php echo $video['title']; ?>
                                    </h1>
                                </div>
                                <div class="mvic-info">
                                    <div class="col-xs-3 col-sm-3 col-lg-3">
                                                            <p>
                                                <strong>Genre: </strong>
                                                <a href="https://gostream.tech/genre/action/" title="Action">Action</a>, <a href="https://gostream.tech/genre/thriller/" title="Thriller">Thriller</a>, <a href="https://gostream.tech/genre/sci-fi/" title="Sci-Fi">Sci-Fi</a>                    </p>
                                                                            <p>
                                                <strong>Actor: </strong>
                                                <a target="_blank" href="https://gostream.tech/actor/gerard-butler" title="Gerard Butler">Gerard Butler</a>, <a target="_blank" href="https://gostream.tech/actor/jim-sturgess" title="Jim Sturgess">Jim Sturgess</a>, <a target="_blank" href="https://gostream.tech/actor/abbie-cornish" title="Abbie Cornish">Abbie Cornish</a>                    </p>
                                                                            <p>
                                                <strong>Director: </strong>
                                                <a href="#" title="Dean Devlin">Dean Devlin</a>                    </p>
                                        <!--                     for phase 2                <p>
                                                <strong>Country: </strong>
                                                <a href="https://domainname/country/us" title="United States">United States</a> </p>
                                         -->            </div>
                                    <div class="col-xs-3 col-sm-3 col-lg-3">
                                      <p><strong>Duration:</strong>      <time class="" itemprop="duration" datetime="<?php echo Video::getItemPropDuration($video['duration']); ?>" ><?php echo Video::getCleanDuration($video['duration']); ?></time>
                               </p>

                                        <p><strong>Quality:</strong> <span class="quality">HD</span></p>

                                        <p><strong>Release:</strong> 2017</p>

                                        <p><strong>IMDb:</strong> 6.3</p>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                                    <meta itemprop="thumbnailUrl" content="<?php echo $img; ?>" />
                                    <meta itemprop="contentURL" content="<?php echo $global['webSiteRootURL'], $catLink, "video/", $video['clean_title']; ?>" />
                                    <meta itemprop="embedURL" content="<?php echo $global['webSiteRootURL'], "videoEmbeded/", $video['clean_title']; ?>" />
                                    <meta itemprop="uploadDate" content="<?php echo $video['created']; ?>" />
                                    <meta itemprop="description" content="<?php echo $video['title']; ?> - <?php echo $video['description']; ?>" />
                                
                                    <h1 itemprop="name">
                                        <?php echo $video['title']; ?>
                                        <small>
                                            <?php
                                            $video['tags'] = Video::getTags($video['id']);
                                            foreach ($video['tags'] as $value) {
                                                if ($value->label === __("Group")) {
                                                    ?>
                                                    <span class="label label-<?php echo $value->type; ?>"><?php echo $value->text; ?></span>

                                                    <?php
                                                }
                                            }
                                            ?>
                                        </small>
                                    </h1>
                                </div> 
                            </div>

                    <div class="col-xs-12 col-sm-1 col-md-1 col-lg-1"></div>
                    <div class="col-xs-12 col-sm-11 col-md-ll col-lg-11"> 
             </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12  list-group-item">                            
                        <?php
                        if (!empty($playlist_id)) {
                            include './include/playlist.php';
                            ?>
                            <script>
                                $(document).ready(function () {
                                    Cookies.set('autoplay', true, {
                                        path: '/',
                                        expires: 365
                                    });
                                });
                            </script>

                            <?php
                        } else if (!empty($autoPlayVideo)) {
                            ?>
                            <div class="col-lg-12 col-sm-12 col-xs-12 autoplay text-muted" style="display: none;">
                                <strong>
                                    <?php
                                    echo __("Up Next");
                                    ?>
                                </strong>
                                <span class="pull-right">
                                    <span>
                                        <?php
                                        echo __("Autoplay");
                                        ?>
                                    </span>
                                    <span>
                                        <i class="fa fa-info-circle" data-toggle="tooltip" data-placement="bottom"  title="<?php echo __("When autoplay is enabled, a suggested video will automatically play next."); ?>"></i>
                                    </span>
                                    <input type="checkbox" data-toggle="toggle" data-size="mini" class="saveCookie" name="autoplay">
                                </span>
                            </div>
                            <div class="col-lg-3 col-sm-3 col-xs-3 bottom-border autoPlayVideo" itemscope itemtype="http://schema.org/VideoObject" style="display: none;" >
                                <a href="<?php echo $global['webSiteRootURL'], $catLink; ?>video/<?php echo $autoPlayVideo['clean_title']; ?>" title="<?php echo $autoPlayVideo['title']; ?>" class="videoLink">
                                    <div class="col-lg-5 col-sm-5 col-xs-5 nopadding thumbsImage">
                                        <?php
                                        $imgGif = "";
                                        if (file_exists("{$global['systemRootPath']}videos/{$autoPlayVideo['filename']}.gif")) {
                                            $imgGif = "{$global['webSiteRootURL']}videos/{$autoPlayVideo['filename']}.gif";
                                        }
                                        if ($autoPlayVideo['type'] !== "audio") {
                                            $img = "{$global['webSiteRootURL']}images/{$autoPlayVideo['postername']}.jpg";
                                            $img_portrait = ($autoPlayVideo['rotation'] === "90" || $autoPlayVideo['rotation'] === "270") ? "img-portrait" : "";
                                        } else {
                                            $img = "{$global['webSiteRootURL']}view/img/audio_wave.jpg";
                                            $img_portrait = "";
                                        }
                                        ?>
                                        <img src="<?php echo $img; ?>" alt="<?php echo $autoPlayVideo['title']; ?>" class="img-responsive <?php echo $img_portrait; ?>  rotate<?php echo $autoPlayVideo['rotation']; ?>" height="130px" itemprop="thumbnail" />
                                        <?php
                                        if (!empty($imgGif)) {
                                            ?>
                                            <img src="<?php echo $imgGif; ?>" style="position: absolute; top: 0; display: none;" alt="<?php echo $autoPlayVideo['title']; ?>" id="thumbsGIF<?php echo $autoPlayVideo['id']; ?>" class="thumbsGIF img-responsive <?php echo $img_portrait; ?>  rotate<?php echo $autoPlayVideo['rotation']; ?>" height="130px" />
                                        <?php } ?>
                                        <meta itemprop="thumbnailUrl" content="<?php echo $img; ?>" />
                                        <meta itemprop="contentURL" content="<?php echo $global['webSiteRootURL'], $catLink, "video/", $autoPlayVideo['clean_title']; ?>" />
                                        <meta itemprop="embedURL" content="<?php echo $global['webSiteRootURL'], "videoEmbeded/", $autoPlayVideo['clean_title']; ?>" />
                                        <meta itemprop="uploadDate" content="<?php echo $autoPlayVideo['created']; ?>" />

                                        <span class="glyphicon glyphicon-play-circle"></span>
                                        <time class="duration" itemprop="duration" datetime="<?php echo Video::getItemPropDuration($autoPlayVideo['duration']); ?>"><?php echo Video::getCleanDuration($autoPlayVideo['duration']); ?></time>
                                    </div>
                                    <div class="col-lg-7 col-sm-7 col-xs-7 videosDetails">
                                        <div class="text-uppercase row"><strong itemprop="name" class="title"><?php echo $autoPlayVideo['title']; ?></strong></div>
                                        <div class="details row text-muted" itemprop="description">
                                            <div>
                                                <strong><?php echo __("Category"); ?>: </strong>
                                                <span class="<?php echo $autoPlayVideo['iconClass']; ?>"></span> 
                                                <?php echo $autoPlayVideo['category']; ?>
                                            </div>
                                            <div>
                                                <strong class=""><?php echo number_format($autoPlayVideo['views_count'], 0); ?></strong> <?php echo __("Views"); ?>
                                            </div>
                                            <div><strong><?php echo $autoPlayVideo['creator']; ?></strong></div>

                                        </div>
                                        <div class="row">
                                            <?php
                                            if (!empty($autoPlayVideo['tags'])) {
                                                foreach ($autoPlayVideo['tags'] as $autoPlayVideo2) {
                                                    if ($autoPlayVideo2->label === __("Group")) {
                                                        ?>
                                                        <span class="label label-<?php echo $autoPlayVideo2->type; ?>"><?php echo $autoPlayVideo2->text; ?></span>
                                                        <?php
                                                    }
                                                }
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </a>
                            </div>
                            <?php
                        }
                        ?>
                        <div class="col-lg-12 col-sm-12 col-xs-12">
                            <?php
                            echo $config->getAdsense();
                            ?>
                        </div>

                        <!-- videos List -->
                        <div id="videosList">
                            <?php include './videosList.php'; ?>                                    
                        </div>
                        <!-- End of videos List -->

                        <script>
                            var fading = false;
                            $(document).ready(function () {

                                $("input.saveCookie").each(function () {
                                    var mycookie = Cookies.get($(this).attr('name'));
                                    console.log($(this).attr('name'));
                                    console.log(mycookie);
                                    if (mycookie && mycookie == "true") {
                                        $(this).prop('checked', mycookie);
                                        $('.autoPlayVideo').slideDown();
                                    }
                                });
                                $("input.saveCookie").change(function () {
                                    console.log($(this).attr('name'));
                                    console.log($(this).prop('checked'));
                                    var auto = $(this).prop('checked');
                                    if (auto) {
                                        $('.autoPlayVideo').slideDown();
                                    } else {
                                        $('.autoPlayVideo').slideUp();
                                    }
                                    Cookies.set($(this).attr("name"), auto, {
                                        path: '/',
                                        expires: 365
                                    });
                                });
                                setTimeout(function () {
                                    $('.autoplay').slideDown();
                                }, 1000);
                                // Total Itens <?php echo $total; ?>

                            });
                        </script>
                    </div>
              
                </div>
                </div>
                <?php
            } else {
                ?>
              
                <div class="alert alert-warning">
                    <span class="glyphicon glyphicon-facetime-video"></span> <strong><?php echo __("Warning"); ?>!</strong> <?php echo __("We have not found any videos or audios to show"); ?>.
                </div>
            <?php } ?>  

        </div>
        <?php
        include 'include/footer.php';
        ?>

        <script src="<?php echo $global['webSiteRootURL']; ?>js/videojs-persistvolume/videojs.persistvolume.js" type="text/javascript"></script>
        <script src="<?php echo $global['webSiteRootURL']; ?>js/webui-popover/jquery.webui-popover.min.js" type="text/javascript"></script>
        <script src="<?php echo $global['webSiteRootURL']; ?>js/bootstrap-list-filter/bootstrap-list-filter.min.js" type="text/javascript"></script>
        <script src="<?php echo $global['webSiteRootURL']; ?>js/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
    </body>
</html>
