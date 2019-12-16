<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <?php if ($error_warning) { ?>
    <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
    <?php } ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="pull-right">
                <button type="submit" form="form-featured" data-toggle="tooltip" title="<?php echo $button_save; ?>"
                        class="btn"><i class="fa fa-check-circle"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>"
                   class="btn"><i class="fa fa-reply"></i></a></div>
            <h1 class="panel-title"><i class="fa fa-puzzle-piece fa-lg"></i> <?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
        <div class="panel-body">
            <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-featured"
                  class="form-horizontal">
                <p>The DoaQa Module allow you to Display FAQ created by you an DoaQa.</p>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-appid"><?php echo $entry_appid; ?></label>
                    <div class="col-sm-10">
                        <input type="text" name="DoaQa_appid" value="<?php echo $DoaQa_appid; ?>"
                               placeholder="<?php echo $entry_appid; ?>" id="input-appid" class="form-control"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="input-appsecret"><?php echo $entry_secret; ?></label>
                    <div class="col-sm-10">
                        <input type="text" name="DoaQa_secret" value="<?php echo $DoaQa_secret; ?>"
                               placeholder="<?php echo $entry_secret; ?>" id="input-appsecret" class="form-control"/>
                    </div>
                </div>

                <?php if($DoaQa_languages){?>
               <div class='col-sm-2'>
                <label class="control-label" for="input-appsecret">DoaQa Contents</label>
                </div>
                <div class='col-sm-10'>
                <table width="100%">
                        <tr>
                            <th>Title</th>
                             <th>Language</th>
                             <th>Display</th>
                        </tr>
                <?php foreach($DoaQa_languages->Languages as $sec)
                {
                ?>
                        <tr>
                            <td><?php echo $sec->title; ?></td>
                <td><a href="<?php echo $Module_url.'&qa='.$sec->id; ?>"><?php echo $Module_url.'&qa='.$sec->id;?></a></td>
                            <td><select id="DqaQa_language_dropdown" name="DoaQa_language_dropdown[]">
                <?php foreach($sec->languages as $sec2)
                {?>
                <option value='<?php echo $sec->id.':'.$sec2->Id; ?>'><?php echo $sec2->Name; ?></option>
                <?php } ?>
                </select></td>

                        </tr>
                <?php } ?>
                        </table></div>
              <!--  <div class="form-group">
                <label class="col-sm-2 control-label" for="DqaQa_language_dropdown"><?php echo $entry_selectTitle; ?></label>
                <div class="col-sm-10">
                <select id="DqaQa_title_dropdown" name="DqaQa_title_dropdown">
                <?php foreach($DoaQa_languages->Languages as $sec)
                {?>
                <option value='<?php echo $sec->id; ?>'><?php echo $sec->title; ?></option>
                <?php } ?>
                </select>
                </div>
                </div>


                <div class="form-group">
                <label class="col-sm-2 control-label" for="DqaQa_language_dropdown"><?php echo  $entry_selectLanguage; ?></label>
                <div class="col-sm-10"><select id="DqaQa_language_dropdown" name="DqaQa_language_dropdown">

                </select>
                </div>
                </div>-->
                <?php } ?>


            </form>
        </div>
        <div class="col-sm-12">
            <?php ?>

        </div>
    </div>
</div>
<?php echo $footer; ?>