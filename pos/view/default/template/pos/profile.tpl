<?php echo $header; ?><?php echo $column_left; ?>
    <h3 class="text-center mb-4">Profile</h3>
    <div class="card card-body">
        <div class="card profile">
            <div class="profile__img">
                <?php if(empty($image)){ ?>
                    <img style="width:100px;" src="../image/cache/no_image-45x45.png" alt="">
                <?php }else { ?>
                    <img style="width:100px;" src="../image/cache/no_image-45x45.png" alt="">
                <?php } ?>
                <!--<input type="file" class="zmdi zmdi-camera profile__img__edit" />-->
            </div>
            <div class="profile__info">
                <h1><?php echo $store_name; ?></h1>
                <p><?php echo $proprietor; ?></p>
                <p><?php echo $store_address; ?></p>
                <ul class="icon-list">
                    <li><i class="zmdi zmdi-face"></i><?php echo $firstname." ".$lastname; ?></li>
                    <li><i class="zmdi zmdi-phone"></i> <?php echo $telephone; ?></li>
                    <li><i class="zmdi zmdi-email"></i> <?php echo $email; ?></li>
                </ul>
            </div>
        </div>
    </div>
<?php echo $footer; ?>
       