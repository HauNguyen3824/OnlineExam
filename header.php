
<div class="site-mobile-menu site-navbar-target">
		<div class="site-mobile-menu-header">
			<div class="site-mobile-menu-close">
				<span class="icofont-close js-menu-toggle"></span>
			</div>
		</div>
		<div class="site-mobile-menu-body">
		</div>
	</div>

	<nav class="site-nav">
		<div class="container">
			<div class="menu-bg-wrap">
				<div class="site-navigation">
					<div class="row g-0 align-items-center">
						<div class="col-4 logo">
							<img src="images/logo.png">
							<a href="index.php" class="logo m-0 float-start"><span class="text-primary">MindBridge <br> Institute</span></a>
						</div>
						<div class="col-4 text-center">
							<ul class="js-clone-nav d-none d-lg-inline-block text-start site-menu mx-auto fw-bold">
								<li class="active"><a href="index.php">TRANG CHỦ</a></li>
								<li class="has-children">
									<a href="#">TRANG</a>
									<ul class="dropdown">
										<?php if($is_logged_in): 
											if($user_type == 0 && $user_type != 2): ?>
												<li class="fw-normal" style="width:fit-content;"><a href="QuanLyNguoiDung.php">Quản lý người dùng</a></li>
											<?php endif; ?>
											<?php if($user_type == 2 || $user_type == 0): ?>
												<li class="fw-normal" style="width:fit-content;"><a href="<?php echo $is_logged_in? 'TaoDeThi.php' : 'DangNhap.php' ?>">Tạo Đề Thi</a></li>
												<li class="fw-normal" style="width:fit-content;"><a href="<?php echo $is_logged_in? 'questionsBank.php' : 'DangNhap.php' ?>">Ngân Hàng Đề Thi</a></li>
												<li class="fw-normal" style="width:fit-content;"><a href="<?php echo $is_logged_in? 'QuanLyDeThi.php' : 'DangNhap.php' ?>">Ngân Hàng Câu Hỏi</a></li>
											<?php endif; ?>
										<?php endif; ?>
										<li class="fw-normal" style="width:fit-content;"><a href="<?php echo $is_logged_in? 'LichSuCuocThi.php' : 'DangNhap.php' ?>">Thống Kê</a></li>

									</ul>
								</li>
								<li><a href="LienHe.php">LIÊN HỆ</a></li>
							
						</div>
						<?php if(!$is_logged_in): ?>
						<div class="col-4 text-end">
							<div class="d-flex justify-content-end align-items-center">
								<a href="DangNhap.php" class="auth-btn login-btn text-decoration-none fw-bold">
									<i class="bi bi-box-arrow-in-right me-1"></i>
									ĐĂNG NHẬP
								</a>
								<a href="DangKy.php" class="auth-btn register-btn text-decoration-none fw-bold">
									<i class="bi bi-person-plus me-1"></i>
									ĐĂNG KÝ
								</a>
								<a href="#" class="burger ms-3 float-end site-menu-toggle js-menu-toggle d-inline-block d-lg-none light">
									<span></span>
								</a>
							</div>
						</div>
						<?php endif; ?>
						<?php if($is_logged_in): ?>
							<div class="col-4 text-end">
							<div class="d-flex justify-content-end align-items-center">
								<a href="ChinhSuaThongTinCaNhan.php" class="auth-btn userInfo-btn text-decoration-none fw-bold" value="<?php echo $user_id; ?>">
									<?php echo htmlspecialchars($user_fullname); ?>
								</a>
								<a href="logout.php" class="auth-btn logout-btn text-decoration-none fw-bold">
									<i class="bi bi-door-open-fill me-1"></i>
									ĐĂNG XUẤT
								</a>
								<a href="#" class="burger ms-3 float-end site-menu-toggle js-menu-toggle d-inline-block d-lg-none light">
									<span></span>
								</a>
							</div>
						</div>
						<?php endif ?>

					</div>
				</div>
			</div>
		</div>
	</nav>