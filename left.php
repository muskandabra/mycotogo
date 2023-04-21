<?php $frontPageName=basename($_SERVER['PHP_SELF']);

include_once(PATH."includes/accessRights/manageLeftNav.php");

include_once(PATH."classes/clsFaq.php");
include_once(PATH."private/settings.php");

?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script> 

<link rel="stylesheet" href="https://code.jquery.com/ui/1.9.2/themes/base/jquery-ui.css" />

<script src="https://code.jquery.com/jquery-1.8.3.js"></script> 

<script src="https://code.jquery.com/ui/1.9.2/jquery-ui.js"></script>

<script src="assets/scripts/popup.js" type="text/javascript"></script>

<link type="text/css" rel="stylesheet" href="assets/css/popup.css">

<link type="text/css" rel="stylesheet" href="assets/css/lightbox-form.css">

<script src="assets/scripts/lightbox-form.js" type="text/javascript"></script>

<link href="style.css" rel="stylesheet" type="text/css"/>

		<div class="page-sidebar nav-collapse collapse">

			<!-- BEGIN SIDEBAR MENU -->        

			<ul class="page-sidebar-menu">

				<li>

					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->

					<div class="sidebar-toggler hidden-phone"></div>

					<!-- BEGIN SIDEBAR TOGGLER BUTTON -->

				</li>

				<li <?php if($frontPageName=="" || $frontPageName=="dashboard.php"){?> class="start active"  <?php } ?> >

					<a href="dashboard.php">

					<i class="icon-dashboard"></i> 

					<span class="title">Dashboard</span>

					<span class="selected"></span>

					</a>

				</li>

				<?php 

				

				if($consumerView==1)

				{ 

				if($_SESSION['usertype']!='Consumer')

				{?>

				<li <?php if($frontPageName=="" || $frontPageName=="consumer.php" || $frontPageName=="additional_reg.php" || $frontPageName=='addconsumerform.php' || $frontPageName=='addconsumerform2.php' || $frontPageName=='consumerpayment.php' || $frontPageName=='reminder.php' || $frontPageName=='showdetails.php'){?> class="start active"  <?php } ?> >

					<a href="consumer.php">

					

					<i class="icon-group"></i> 

					<span class="title">Manage Consumers</span>

					<span class="selected"></span>

					</a>

				</li>

				<li <?php if($frontPageName=="" || $frontPageName=="profile.php"){?> class="start active"  <?php } ?> >

					<a href="profile.php">

					<i class="icon-user"></i> 

					<span class="title">Manage Profile</span>

					<span class="selected"></span>

					</a>

				</li>

				<?php 

				}}

				if($_SESSION['usertype']=='Consumer')

				{

					$sel=mysqli_query($dbconnection,"Select consumer_id from tbl_consumermaster where user_id='".$_SESSION['sessuserid']."'");

					$fetch=mysqli_fetch_object($sel);

					if(isset($_SESSION['bookdetail']) && $_SESSION['bookdetail']!='')

						$consumer_id = base64_decode($_SESSION['bookdetail']);

					else

						$consumer_id = $fetch->consumer_id;

					?>

				<li <?php if($frontPageName=="" || $frontPageName=="showbook.php" ){?> class="start active"  <?php } ?> >

					<a href="showbook.php?n=<?php echo base64_encode($consumer_id);?>">

					<i class="icon-briefcase"></i> 

					<span class="title">View Record Book</span>

					<span class="selected"></span>

					</a>

				</li>

				

				<li <?php if($frontPageName=="" || $frontPageName=="userbook.php" ){?> class="start active"  <?php } ?> >

					<a href="userbook.php">

					<i class="icon-briefcase"></i> 

					<span class="title">Open Briefcase</span>

					<span class="selected"></span>

					</a>

				</li>

				<li <?php if($frontPageName=="" || $frontPageName=="showbook.php"){?> class="start active"  <?php } ?> style="display:none;" >

					<a href="showbook.php?n=<?php echo base64_encode($fetch->consumer_id);?>">

					<i class="icon-home"></i> 

					<span class="title">View /Create Folders</span>

					<span class="selected"></span>

					</a>

				</li>

				<li <?php if($frontPageName=="" || $frontPageName=="profile.php"){?> class="start active"  <?php } ?> >

					<a href="profile.php">

					<i class="icon-user"></i> 

					<span class="title">Manage Profile</span>

					<span class="selected"></span>

					</a>

				</li>

				<li <?php if($frontPageName=="" || $frontPageName=="faq.php"){?> class="start active"  <?php } ?> >

					<a href="#" onClick="openFAQPopUp()">

					<i class="icon-question"></i>

					<span class="title">FAQ</span>

					<span class="selected"></span>

					</a>

				</li>

				<li <?php if($frontPageName=="" || $frontPageName=="userguides.php" ){?> class="start active"  <?php } ?> >

					<a href="#" onClick="openUserUserGuidePopUp()">

					<i class="icon-medkit"></i> 

					<span class="title">User Guides</span>

					<span class="selected"></span>

					</a>

				</li>

				<li <?php if($frontPageName=="" || $frontPageName=="support.php" ){?> class="start active"  <?php } ?> >

					<a href="#" onClick="openUserUserSupport()">

					<i class="icon-phone"></i> 

					<span class="title">Support</span>

					<span class="selected"></span>

					</a>

				</li>

				<?php

				}

				if($_SESSION['usertype']=='admin')

				{?>

						<li <?php if($frontPageName=="" || $frontPageName=="user.php"){?> class="start active"  <?php } ?> >

							<a href="user.php">

							<i class="icon-home"></i>

							<span class="title">Manage User</span>

							<span class="selected"></span>

							</a>

						</li>

					<?php }

					if($noticationView==1)

					{	?>

						<li <?php if($frontPageName=="" || $frontPageName=="notifications.php" ){?> class="start active"  <?php } ?> >

							<a href="notifications.php">

							<i class="icon-bell"></i>

							<span class="title">Manage Notifications</span>

							<span class="selected"></span>

							</a>

						</li>

						<?php 

					}	?>

					<li <?php if($frontPageName=="" || $frontPageName=="templates.php" ){?> class="start active"  <?php } ?> >

						<a href="templates.php">

						<i class="icon-bell"></i>

						<span class="title">Manage Templates</span>

						<span class="selected"></span>

						</a>

					</li>

					
                     <li <?php if($frontPageName=="" || $frontPageName=="templates.php" ){?> class="start active"  <?php } ?> >

						<a href="templates.php">

						<i class="icon-bell"></i>

						<span class="title">Manage Templates</span>

						<span class="selected"></span>

						</a>

					</li>
					

			</ul>

			<!-- END SIDEBAR MENU -->

		</div>

		<div id="faqshadow" style="display:none;" ></div>

		<div id="faq">

		<h3>FAQ <a style="float:right;" id="close" class="btn red" href="#" onclick="return removeShadowwithFaq();">

				<i class="icon-remove"></i>

			</a></h3>

		

			<?php $objFaq= new Faq(); 

				$resFaq=$objFaq->SelectFaq();

				$sr=1;

				if(mysqli_num_rows($resFaq)>0)

				{

					while($qans=mysqli_fetch_object($resFaq))

					{

						?><p class="question"><?php echo '<span>'.$sr.":".'</span>'; echo stripslashes($qans->faqauestion);?></p>

						<p class="answer"> <?php echo '<span>'."Ans:".'</span>'; echo stripslashes($qans->faqanswer); ?></p><?php 

						$sr++;

					}

				}

			?>

			

		</div>

		<div id="userguide">

		<h3>CheckList <a style="float:right;" id="close" class="btn red" href="#" onclick="return removeShadowwithuserGuide();">

				<i class="icon-remove"></i>

			</a></h3>

			<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.</p>

			

		</div>

		

		<div id="usersupport">

		<h3>User Support <a style="float:right;" id="close" class="btn red" href="#" onclick="return removeShadowwithsupport();">

				<i class="icon-remove"></i>

			</a></h3>

			<p>	How can we help?



				<p>1. Answers to popular topics can be found on our FAQ(insert link to FAQ page here) page.</p>



				<p>2. Review our User Guides(insert link to USER Guides page here) and videos.</p>



				<p>3. Contact us at: 1-888-362-5025 ext 3 toll free Monday- Friday from 9:00 a.m.- 5:00 p.m. M.S.T for technical assistance.</p>



				<p>4. Open an email support ticket at:  support@mycotogo.com</p>

			</p>

			

		</div>

			<div class="backdrop"></div>

			<div class="light_box" >

			  <div class="close" style="float:right">X</div>

			  <div class="renameform" style="padding:20px;"></div>

			</div>

			<div id="shadowing" style="display:none;"></div>

<script>

function openFAQPopUp()

{

	document.getElementById('faq').style.display='Block';

	document.getElementById('faqshadow').style.display='Block';

}	

function removeShadowwithFaq()

{

	document.getElementById('faq').style.display='none';

	document.getElementById('faqshadow').style.display='none';

}

function openUserUserGuidePopUp()

{

	document.getElementById('userguide').style.display='Block';

	document.getElementById('faqshadow').style.display='Block';

}

function removeShadowwithuserGuide()

{

	document.getElementById('userguide').style.display='none';

	document.getElementById('faqshadow').style.display='none';

}

function openUserUserSupport()

{

	document.getElementById('usersupport').style.display='Block';

	document.getElementById('faqshadow').style.display='Block';

}

function removeShadowwithsupport()

{

document.getElementById('usersupport').style.display='none';

	document.getElementById('faqshadow').style.display='none';

}

</script>