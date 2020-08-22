<?php
use yii\helpers\Url;
use yii\helpers\Html;
?>
<header id="navbar" style="z-index:3999;">
    <div id="navbar-container" class="boxed">
        <!--Brand logo & name-->
        <!--================================-->
        <div class="navbar-header">
            <a href="<?= Yii::$app->homeUrl; ?>" class="navbar-brand">
                <img src="/images/logo.png" class="brand-icon">
                <div class="brand-title">
                    <span class="brand-text"><?= Yii::$app->name; ?></span>
                </div>
            </a>
        </div>
        <!--================================-->
        <!--End brand logo & name-->


        <!--Navbar Dropdown-->
        <!--================================-->
        <div class="navbar-content">
            <ul class="nav navbar-top-links">

                <!--Navigation toogle button-->
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <li class="tgl-menu-btn" style="top:0;">
                    <a class="mainnav-toggle" href="#">
                        <i class="pli-list-view"></i>
                    </a>
                </li>
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <!--End Navigation toogle button-->



                <!--Search-->
                <!--
                <li>
                    <div class="custom-search-form">
                        <label class="btn btn-trans" for="search-input" data-toggle="collapse" data-target="#nav-searchbox">
                            <i class="pli-magnifi-glass"></i>
                        </label>
                        <form>
                            <div class="search-container collapse" id="nav-searchbox">
                                <input id="search-input" type="text" class="form-control" placeholder="">
                            </div>
                        </form>
                    </div>
                </li>
                -->
                <!--End Search-->

            </ul>
            <ul class="nav navbar-top-links">

                <li class="dropdown">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                        <i class="pli-flag"></i>
                    </a>
                    
                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right panel-default">
                        <ul class="head-list">
                            <li>
                                <a href="<?= Url::to(['/site/language', 'lang' => 'zh-CN'])?>"><i class="pli-china icon-lg icon-fw"></i> 简体中文</a>
                            </li>
                            <li>
                                <a href="<?= Url::to(['/site/language', 'lang' => 'en'])?>"><i class="pli-united-kingdom icon-lg icon-fw"></i> English</a>
                            </li>
                        </ul>
                    </div>
                </li>


                <!--User dropdown-->
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <li id="dropdown-user" class="dropdown">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle text-right">
                        <span class="ic-user pull-right">
                            <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                            <!--You can use an image instead of an icon.-->
                            <!--<img class="img-circle img-user media-object" src="img/profile-photos/1.png" alt="Profile Picture">-->
                            <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                            <i class="pli-male"></i>
                        </span>
                        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                        <!--You can also display a user name in the navbar.-->
                        <!--<div class="username hidden-xs">Aaron Chavez</div>-->
                        <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                    </a>


                    <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right panel-default">
                        <ul class="head-list">
                            <li>
                            	<?= Html::a('<i class="pli-male icon-lg icon-fw"></i> '.Yii::t('app/menu', 'profile'), ['/user/profile']) ?>
                            </li>
                            <li>
                            	<?= Html::a('<i class="pli-password-shopping icon-lg icon-fw"></i> '.Yii::t('app/menu', 'change_password'), ['/user/edit-password']) ?>
                            </li>
                            <li>
                            	<?= Html::a('<i class="pli-unlock icon-lg icon-fw"></i> '.Yii::t('app/menu', 'logout'), ['/site/logout']) ?>
                            </li>
                        </ul>
                    </div>
                </li>
                <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
                <!--End user dropdown-->

				<!-- 
                <li>
                    <a href="#" class="aside-toggle">
                        <i class="pli-dot-vertical"></i>
                    </a>
                </li>
                 -->
            </ul>
        </div>
        <!--================================-->
        <!--End Navbar Dropdown-->

    </div>
</header>
