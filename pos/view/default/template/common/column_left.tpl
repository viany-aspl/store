
<aside class="sidebar sidebar--hidden">
                <div class="scrollbar-inner">
                    <div class="user">
                        <div class="user__info" data-toggle="dropdown">
                            <img class="user__img" src="../image/cache/no_image-45x45.png" alt="">
                            <div>
                                <div class="user__name"><?php echo $UserNameShow; ?></div>
                                <div class="user__email"><?php echo $Usergroupname; ?></div>
                            </div>
                        </div>

                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="<?php echo $profile_link; ?>">View Profile</a>
                            
                            <a class="dropdown-item" href="<?php echo $logout_action; ?>">Logout</a>
                        </div>
                    </div>
        <ul id="menu" class="navigation">
			<?php foreach ($menus as $menu) { ?>
					<li id="<?php echo $menu['id']; ?>" <?php if ($menu['children']) { echo 'class="navigation__sub"'; } ?> >
						<?php if ($menu['href']) { ?>
                    <a href="<?php echo $menu['href']; ?>"><img class="web-icon-thumb" src="<?php echo $menu['icon']; ?>"/> <span><?php echo $menu['name']; ?></span>
						<?php if ($menu['children']) { ?>
							<i style="float: right;color: rgb(33, 150, 243)" class="zmdi zmdi-plus"></i> 
						<?php } ?>
					</a>
                <?php } else { ?>
                    <a class="parent"><img class="web-icon-thumb" src="<?php echo $menu['icon']; ?>"/> <span><?php echo $menu['name']; ?></span>
					
					</a>
					 
                <?php } ?>
				
      <?php if ($menu['children']) { ?>
      <ul>
        <?php foreach ($menu['children'] as $children_1) { ?>
        <li>
          <?php if ($children_1['href']) { ?>
          <a href="<?php echo $children_1['href']; ?>" class="">
              <img class="web-icon-thumb" src="<?php echo $children_1['icon']; ?>"/>
              <?php echo $children_1['name']; ?>
          </a>
          <?php } else { ?>
          <a class="parent"><?php echo $children_1['name']; ?></a>
          <?php } ?>
          <?php if ($children_1['children']) { ?>
          <ul>
            <?php foreach ($children_1['children'] as $children_2) { ?>
            <li>
              <?php if ($children_2['href']) { ?>
              <a href="<?php echo $children_2['href']; ?>" class="<?php echo $children_2['icon']; ?>"><?php echo $children_2['name']; ?></a>
              <?php } else { ?>
              <a class="parent"><?php echo $children_2['name']; ?></a>
              <?php } ?>
              <?php if ($children_2['children']) { ?>
              <ul>
                <?php foreach ($children_2['children'] as $children_3) { ?>
                <li><a href="<?php echo $children_3['href']; ?>" class="<?php echo $children_3['icon']; ?>"><?php echo $children_3['name']; ?></a></li>
                <?php } ?>
              </ul>
              <?php } ?>
            </li>
            <?php } ?>
          </ul>
          <?php } ?>
        </li>
        <?php } ?>
      </ul>
	        
      <?php } ?>
    </li>
    <?php } ?>
  </ul>
                  
                </div>
            </aside>
            <aside class="chat" id="chat_main">
			<!--<i class="zmdi zmdi-close" style="cursor: pointer;" onclick="return close_chat()"></i>-->
			
			<div class="answer_div_top" style="display: none;overflow:scroll; height:400px;min-height: 600px;">
				<i onclick="return return_to_question();" class="zmdi zmdi-arrow-left" style="margin-left: 30px;font-size: 20px;color: red;cursor: pointer;"></i>
				<div style="margin-left: 30px;margin-right: 30px;font-weight: bold;font-size: 18px;margin-bottom: 6px;" class="question_div" id="question_div"></div>
				<div style="margin-left: 30px;margin-right: 30px;margin-top: 20px; " class="image_div" id="image_div"></div>
				<div style="margin-left: 30px;margin-right: 30px;" class="answer_div" id="answer_div"></div>
				<div style="margin-left: 30px;margin-right: 30px;height:100px;"></div>
			</div>
                <div id="chat_qu">
                <div class="chat__header">
				
                    <h2 class="chat__title">FAQ </h2>

                </div>


				<table id="data-tables" class="" data-paging='false' data-ordering='false' data-info='false'data-abuttons='false' data-scrollY='true' data-scrollX='200px' data-scrollCollapse='true'>
                                <thead class="thead-default">
                                    <tr>
                                        <th style="display: none;"></th>
                                        
                                        
                                    </tr>
                                </thead>
                                
                                <tbody>
								<?php $a=1; foreach($faqs as $faq){ ?>
                                    <tr>
                                        <td>
										<?php //print_r($faq); ?>
										<a onclick="return show_answer(<?php echo ($faq['faq_id']); ?>);" class="listview__item">
                        

                        <div class="listview__content">
                            <div class="listview__heading" id="question_<?php echo ($faq['faq_id']); ?>"><?php echo ($faq['question']); ?></div>
                            <span style="display: none;" id="answer_<?php echo ($faq['faq_id']); ?>"><?php echo ($faq['answer']); ?></span>
							<span style="display: none;" id="image_<?php echo ($faq['faq_id']); ?>">
							<?php if(!empty($faq['image'])){ ?>
								<img style="min-width: 200px;min-height: 200px;" src="<?php echo ($faq['image']); ?>" />
							<?php } ?>
							</span>
                        </div>
                    </a>
					
					
					</td>
                                       
                                        
                                    </tr>
                                <?php $a++; } ?> 
                                </tbody>
                            </table>
				
                </div>
            </aside>
			<script>
		
			$(".ma-backdrop").click(function(){
				alert("The paragraph was clicked.");
				$("#chat_main").show();
			});
			function close_chat()
			{
				//$(".chat").removeClass("toggled");
				$("#chat_main").hide();
				return false;
			}
			function return_to_question()
			{
				$('.answer_div_top').hide();
				$('#chat_qu').show();
				return false;
			}
			function show_answer(faq_id)
			{
				var question=$("#question_"+faq_id).html();
				$("#question_div").html(question);
				
				var answer=$("#answer_"+faq_id).html();
				var image1=$("#image_"+faq_id).html();
				$("#answer_div").html(answer);
				$("#image_div").html(image1);
				$('#chat_qu').hide();
				$('.answer_div_top').show();
				return false;
			}
			</script>
                        <section class="content content--full">