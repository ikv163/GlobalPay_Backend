<?php
use yii\helpers\Html;
use backend\models\Admin;
use sunnnnn\nifty\auth\components\helpers\MenuHelper;
use sunnnnn\nifty\auth\components\widgets\Menu;
?>
<nav id="mainnav-container">
    <div id="mainnav">


        <!--OPTIONAL : ADD YOUR LOGO TO THE NAVIGATION-->
        <!--It will only appear on small screen devices.-->
        <!--================================
        <div class="mainnav-brand">
            <a href="index.html" class="brand">
                <img src="img/logo.png" alt="Nifty Logo" class="brand-icon">
                <span class="brand-text">Nifty</span>
            </a>
            <a href="#" class="mainnav-toggle"><i class="pci-cross pci-circle icon-lg"></i></a>
        </div>
        -->



        <!--Menu-->
        <!--================================-->
        <div id="mainnav-menu-wrap">
            <div class="nano">
                <div class="nano-content">

                    <!--Profile Widget-->
                    <!--================================-->
                    <div id="mainnav-profile" class="mainnav-profile">
                        <div class="profile-wrap text-center">
                            <div class="pad-btm">
                                <img class="img-circle img-md profile-photo" src="<?= empty(Yii::$app->user->identity->photo) ? Admin::getProfilePhotos(true) : Yii::$app->user->identity->photo; ?>" alt="Profile Picture">
                            </div>
                            <a href="#profile-nav" class="box-block" data-toggle="collapse" aria-expanded="false">
                                <span class="pull-right dropdown-toggle">
                                    <i class="dropdown-caret"></i>
                                </span>
                                <span class="mnp-name"><?= empty(Yii::$app->user->identity->username) ? '-' : Yii::$app->user->identity->username; ?></span>
                                <span class="mnp-desc"><?= empty(Yii::$app->user->identity->username) ? '' : '' ; ?></span>
                            </a>
                        </div>
                        <div id="profile-nav" class="collapse list-group bg-trans">
                        <?php 
                            echo Html::a('<i class="pli-male icon-lg icon-fw"></i> '.Yii::t('app/menu', 'profile'), ['/user/profile'], ['class' => 'list-group-item']);
                            echo Html::a('<i class="pli-password-shopping icon-lg icon-fw"></i> '.Yii::t('app/menu', 'change_password'), ['/user/edit-password'], ['class' => 'list-group-item']);
                            echo Html::a('<i class="pli-unlock icon-lg icon-fw"></i> '.Yii::t('app/menu', 'logout'), ['/site/logout'], ['class' => 'list-group-item']);
                        
                        ?>
                        </div>
                    </div>


                    <!--Shortcut buttons-->
                    <!--================================-->
                    <!--================================-->
                    <!--End shortcut buttons-->

					<?=
                        Menu::widget([
                            'options' => ['id' => 'mainnav-menu', 'class' => 'list-group'],
                            'items' => MenuHelper::getAssignedMenu(),
                        ]);
                    ?>

                </div>
            </div>
        </div>
        <!--================================-->
        <!--End menu-->

    </div>
</nav>
<!--===================================================-->
<!--END MAIN NAVIGATION-->

