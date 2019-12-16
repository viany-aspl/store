<?php echo $header; ?>
<div class="container">
  <ul class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
    <?php } ?>
  </ul>
  <div class="row"><?php echo $column_left; ?>
    <?php if ($column_left && $column_right) { ?>
    <?php $class = 'col-sm-6'; ?>
    <?php } elseif ($column_left || $column_right) { ?>
    <?php $class = 'col-sm-9'; ?>
    <?php } else { ?>
    <?php $class = 'col-sm-12'; ?>
    <?php } ?>
    <div id="content" class="<?php echo $class; ?>"><?php echo $content_top; ?>
      <h1><?php echo $heading_title; ?></h1>
      <div class="panel-group">
        <?php $counter1=1; foreach($data as $faq){
        if(isset($faq->Title)){
        ?>
        <div class="panel panel-default">
          <div class="panel-heading">
            <h4 class="panel-title">
           <a data-toggle="collapse" href="#collapse<?php echo $counter1;?>"><?php echo $faq->Title;?></a>
            </h4>
          </div>
          <div id="collapse<?php echo $counter1;?>" class="panel-collapse collapse">
            <ul class="list-group">
                <?php
                $questioncounter = 1;
                foreach($faq->Sections[0]->QAs as $qa){
                ?>
              <li class="list-group-item"><h4 class="panel-title">Question <?php echo $questioncounter; ?>: <?php echo $qa->Question;?>
        <!--<a data-toggle="collapse" href="#collapse1"><?php echo $qa->Question;?></a>-->
        </h4>
          </li>
        <li class="list-group-item">Answer: <?php echo htmlspecialchars_decode($qa->Answer);?></li>
         </li>
                <?php $questioncounter++; }?>

            </ul>

          </div>
        </div>
<?php $counter1++;}} ?>
    <div class="panel-footer">Footer</div>
      </div><?php echo $content_bottom; ?></div>
    <?php echo $column_right; ?></div>
</div>
<?php echo $footer; ?> 