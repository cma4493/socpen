					<!-- Begin nav bar -->
					<ul class="nav ace-nav">

						<li class="light-blue">
							<a data-toggle="dropdown" href="#" class="dropdown-toggle">
								<img class="nav-user-photo" src="assets/avatars/avatar.png" alt="<?php echo CurrentUserName();?> Photo" />
								<span class="user-info">
									<small>Welcome,</small>
									<?php echo CurrentUserName();?>
								</span>

								<i class="icon-caret-down"></i>
							</a>

							<ul class="user-menu pull-right dropdown-menu dropdown-yellow dropdown-caret dropdown-close">
								<li>
									<a href="changepwd.php">
										<i class="icon-cog"></i>
										Change password
									</a>
								</li>

								<li class="divider"></li>

								<li>
									<a href="logout.php">
										<i class="icon-off"></i>
										Logout
									</a>
								</li>
							</ul>
						</li>
					</ul>
					<!-- /end nav bar .ace-nav -->