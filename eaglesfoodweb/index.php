<?php 
include "db.php"; 
// Base URL for your admin panel assets
$admin_url = "https://eaglesfood.info/admin/";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Eagles Food - Sermons of William Marion Branham</title>
  
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    :root { --gold: #ffcc00; --primary: #007bff; --primary-hover: #0056b3; }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: 'Poppins', sans-serif; overflow-x: hidden; width: 100%; }

    /* Responsive Social Header */
    .social-header { display: flex; justify-content: space-between; align-items: center; background-color: #f4f4f4; padding: 10px 5%; font-size: 14px; flex-wrap: wrap; gap: 10px; }
    .social-group a { margin-right: 12px; color: #333; text-decoration: none; font-size: 18px; }
    .contact-group { display: flex; flex-wrap: wrap; gap: 15px; }
    .contact-group a { text-decoration: none; color: inherit; }

    /* Main Navigation */
    .main-header { width: 100%; display: flex; justify-content: space-between; align-items: center; padding: 10px 5%; background-color: rgba(0, 0, 0, 0.4); position: absolute; top: 0; z-index: 100; }
    .logo img { width: 80px; height: auto; }
    .nav-links { display: flex; align-items: center; gap: 20px; }
    .nav-links a { text-decoration: none; color: #fff; font-weight: 500; transition: 0.3s; }
    .nav-links a:hover { color: var(--gold); }

    /* Hero & Carousel */
    .hero-section { position: relative; min-height: 100vh; display: flex; flex-direction: column; overflow: hidden; background: #000; }
    .carousel-slide { position: absolute; inset: 0; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.5s ease-in-out; }
    .carousel-slide.active { opacity: 1; }
    .hero-overlay { position: absolute; inset: 0; background: rgba(0, 0, 0, 0.5); z-index: 1; }
    .home-page { position: relative; z-index: 2; padding: 140px 5% 40px; text-align: center; color: #fff; margin: auto 0; }
    .home-page h1 { font-size: clamp(2rem, 5vw, 3.5rem); font-weight: 700; }

    /* Hero Tabs */
    .hero-tabs-container { margin-top: 30px; }
    .hero-tabs-nav { display: flex; justify-content: center; gap: 12px; overflow-x: auto; padding: 10px 0; scrollbar-width: none; }
    .hero-tabs-nav::-webkit-scrollbar { display: none; }
    .hero-tab-item {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #fff;
        padding: 8px 20px;
        border-radius: 50px;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: 0.3s;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .hero-tab-item:hover { background: var(--gold); color: #000; border-color: var(--gold); transform: translateY(-3px); }

    /* Grid Layouts */
    .blog-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; padding: 20px; max-width: 1200px; margin: 0 auto; }
    .card { background: #5261e4; border-radius: 20px; overflow: hidden; color: #fff; transition: 0.3s; height: 100%; }
    .card.hidden { display: none; }
    .card img { width: 100%; height: 250px; object-fit: cover; }
    .card-content { padding: 20px; }

    /* Video Cards */
    .video-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: 0.3s; height: 100%; }
    .video-card:hover { transform: translateY(-5px); }
    .video-thumbnail-wrapper { position: relative; padding-top: 56.25%; background: #000; }
    .video-thumbnail-wrapper img { position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; }
    .play-overlay { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.2); color: #fff; font-size: 2rem; transition: 0.3s; }
    .video-card:hover .play-overlay { background: rgba(0,0,0,0.4); color: var(--gold); }
    .video-title { padding: 12px; font-size: 0.9rem; font-weight: 600; color: #333; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin: 0; }
    .video-item.hidden { display: none; }

    /* Mobile Menu */
    .menu-toggle { display: none; cursor: pointer; flex-direction: column; gap: 5px; z-index: 101; }
    .menu-toggle span { width: 25px; height: 3px; background: #fff; border-radius: 2px; transition: 0.3s; }

    @media (max-width: 768px) {
      .menu-toggle { display: flex; }
      .nav-links { position: fixed; right: -100%; top: 0; height: 100vh; width: 75%; background: rgba(0,0,0,0.95); flex-direction: column; padding-top: 100px; transition: 0.4s; }
      .nav-links.active { right: 0; }
      .social-header { justify-content: center; text-align: center; }
      .hero-tabs-nav { justify-content: flex-start; padding-left: 20px; }
    }
  </style>
</head>
<body>

  <div class="social-header">
    <div class="social-group">
      <?php
      $res = mysqli_query($conn, "SELECT * FROM `social_media`") or die(mysqli_error($conn));
      while ($row1 = mysqli_fetch_assoc($res)): ?>
        <a href="<?= htmlspecialchars($row1['link']) ?>" target="_blank">
          <i class="<?= htmlspecialchars($row1['icon']) ?>"></i>
        </a>
      <?php endwhile; ?>
    </div>
    <div class="contact-group">
      <?php
      $res2 = mysqli_query($conn, "SELECT * FROM `eaglesfood_socialmedia` LIMIT 1");
      while ($row2 = mysqli_fetch_assoc($res2)): ?>
        <div><a href="mailto:<?= $row2['email'] ?>"><i class="far fa-envelope"></i> <?= $row2['email'] ?></a></div>
        <div><i class="fas fa-phone"></i> <?= $row2['phone_number'] ?></div>
      <?php endwhile; ?>
    </div>
  </div>

  <section class="hero-section">
    <?php
    $res3 = mysqli_query($conn, "SELECT * FROM `homepage_manage` LIMIT 1");
    if ($row3 = mysqli_fetch_assoc($res3)):
      $carousel_images = explode(',', $row3['upload_homepage']);
    ?>
      <div class="carousel-container">
        <?php foreach ($carousel_images as $index => $image_path): 
          $trimmed_path = trim($image_path);
          if(!empty($trimmed_path)): ?>
          <div class="carousel-slide <?= ($index === 0) ? 'active' : '' ?>" style="background-image: url('<?= $admin_url . $trimmed_path ?>')"></div>
        <?php endif; endforeach; ?>
      </div>
      <div class="hero-overlay"></div>
      
      <header class="main-header">
        <div class="logo"><img src="<?= $admin_url . $row3['upload_logo'] ?>" alt="Logo"></div>
        <div class="menu-toggle" id="mobile-menu"><span></span><span></span><span></span></div>
        <nav class="nav-links" id="nav-links">
          <a href="#">Home</a>
          <a href="detail_message_page.php">Sermons</a>
          <a href="teachings_list.php">Teachings</a>
          <a href="contact_us.php">Contact</a>
          <div class="d-flex gap-2">
            <a href="login_page.php" class="btn btn-primary btn-sm px-3">Login</a>
            <a href="registration.php" class="btn btn-outline-light btn-sm px-3">Sign Up</a>
          </div>
        </nav>
      </header>

      <div class="home-page">
        <h1><?= htmlspecialchars($row3['title']) ?></h1>
        <p class="mt-3 mx-auto" style="max-width: 800px;"><?= htmlspecialchars($row3['description']) ?></p>
        
        <div class="hero-tabs-container">
            <div class="hero-tabs-nav">
                <a href="detail_message_page.php" class="hero-tab-item"><i class="fas fa-book-open"></i> Messages</a>
                <a href="seals.php" class="hero-tab-item"><i class="fas fa-scroll"></i> Seals</a>
                <a href="church_ages.php" class="hero-tab-item"><i class="fas fa-church"></i> Church Ages</a>
                <a href="teachings_list.php" class="hero-tab-item"><i class="fas fa-leaf"></i> Leaflets</a>
            </div>
        </div>
      </div>
    <?php endif; ?>
  </section>

  <?php
  $res4 = mysqli_query($conn, "SELECT * FROM `web_about_us` LIMIT 1");
  while ($row4 = mysqli_fetch_assoc($res4)): ?>
    <section class="container my-5 py-5">
      <div class="row align-items-center">
        <div class="col-md-5 text-center">
          <img src="<?= $admin_url . $row4['image'] ?>" class="img-fluid rounded-4 shadow-lg" style="max-height: 500px; width: 100%; object-fit: cover;">
        </div>
        <div class="col-md-7 mt-5 mt-md-0 ps-md-5">
          <h6 class="text-primary text-uppercase fw-bold ls-1">About Us</h6>
          <h2 class="display-5 fw-bold mb-4"><?= $row4['title'] ?></h2>
          <p class="lead text-muted border-start border-4 border-primary ps-4 mb-4" style="font-style: italic;">"<?= $row4['message_desciption'] ?>"</p>
          <p class="mb-5"><?= $row4['message_title'] ?></p>
          <div class="d-flex gap-3">
            <a href="detail_message_page.php" class="btn btn-primary px-4 py-2">Read Messages</a>
            <a href="contact_us.php" class="btn btn-outline-dark px-4 py-2">Get in Touch</a>
          </div>
        </div>
      </div>
    </section>
  <?php endwhile; ?>

  <section class="bg-dark py-5 text-white">
    <div class="container text-center mb-5">
      <h2 class="display-6 fw-bold">Spiritual Contributions</h2>
      <div class="mx-auto bg-primary" style="height: 3px; width: 60px;"></div>
    </div>
    <div class="blog-cards" id="cardsContainer">
      <?php
      $res5 = mysqli_query($conn, "SELECT * FROM `contribution_card`") or die(mysqli_error($conn));
      $count = 0;
      while ($row5 = mysqli_fetch_assoc($res5)):
        $count++;
        $hidden = ($count > 3) ? 'hidden' : '';
      ?>
        <div class="card <?= $hidden ?>">
          <img src="<?= $admin_url . $row5['image'] ?>" alt="Card Image">
          <div class="card-content">
            <h4 class="fw-bold mb-3"><?= $row5['title'] ?></h4>
            <p class="small text-light opacity-75 mb-4"><?= $row5['description'] ?></p>
            <a href="<?= $row5['link_page'] ?>" class="btn btn-link text-white p-0 text-decoration-none fw-bold">Next Message →</a>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
    <?php if ($count > 3): ?>
      <div class="text-center mt-5">
        <button class="btn btn-outline-light px-5" id="seeMoreBtn">View All Cards</button>
      </div>
    <?php endif; ?>
  </section>

  <section class="py-5" style="background-color: #f9f9f9;">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-6 fw-bold">Recent Videos</h2>
            <div class="mx-auto bg-primary" style="height: 3px; width: 60px;"></div>
        </div>
        <div class="row g-4" id="videoContainer">
            <?php
            $res_yt = mysqli_query($conn, "SELECT * FROM `eaglesfood_ytvideos` ORDER BY video_id DESC") or die(mysqli_error($conn));
            $yt_count = 0;
            while ($row_yt = mysqli_fetch_assoc($res_yt)):
                $yt_count++;
                $hidden_yt = ($yt_count > 4) ? 'hidden' : '';
                
                // Helper to extract YouTube ID
                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $row_yt['yt_link'], $match);
                $v_id = $match[1] ?? 'default';
                $thumbnail = "https://img.youtube.com/vi/$v_id/mqdefault.jpg";
            ?>
                <div class="col-6 col-md-4 col-lg-3 video-item <?= $hidden_yt ?>">
                    <a href="<?= $row_yt['yt_link'] ?>" target="_blank" class="text-decoration-none">
                        <div class="video-card">
                            <div class="video-thumbnail-wrapper">
                                <img src="<?= $thumbnail ?>" alt="Video Thumbnail">
                                <div class="play-overlay"><i class="fab fa-youtube"></i></div>
                            </div>
                            <h5 class="video-title"><?= htmlspecialchars($row_yt['title']) ?></h5>
                        </div>
                    </a>
                </div>
            <?php endwhile; ?>
        </div>
        <?php if ($yt_count > 4): ?>
            <div class="text-center mt-5">
                <button class="btn btn-outline-primary px-5" id="seeMoreVideos">View All Videos</button>
            </div>
        <?php endif; ?>
    </div>
  </section>

  <footer class="bg-black text-white py-4 border-top border-secondary">
    <div class="container text-center">
      <p class="mb-0 opacity-75 small">
        &copy; <?= date('Y') ?> <span class="text-warning fw-bold">Eagles Food</span>. 
        Developed by <a href="https://stephen-logos.solutions" class="text-primary text-decoration-none">Stephen-Logos Solutions</a>
      </p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Mobile Nav
    const menuBtn = document.getElementById('mobile-menu');
    const navMenu = document.getElementById('nav-links');
    menuBtn.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        const spans = menuBtn.querySelectorAll('span');
        spans[0].style.transform = navMenu.classList.contains('active') ? 'translateY(8px) rotate(45deg)' : 'none';
        spans[1].style.opacity = navMenu.classList.contains('active') ? '0' : '1';
        spans[2].style.transform = navMenu.classList.contains('active') ? 'translateY(-8px) rotate(-45deg)' : 'none';
    });

    // Carousel
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    if(slides.length > 1) {
      setInterval(() => {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add('active');
      }, 6000);
    }

    // See More Contributions
    const seeMoreBtn = document.getElementById('seeMoreBtn');
    if(seeMoreBtn) {
      seeMoreBtn.addEventListener('click', () => {
        document.querySelectorAll('.card.hidden').forEach(card => card.classList.remove('hidden'));
        seeMoreBtn.parentElement.style.display = 'none';
      });
    }

    // See More Videos
    const seeMoreVideosBtn = document.getElementById('seeMoreVideos');
    if(seeMoreVideosBtn) {
        seeMoreVideosBtn.addEventListener('click', () => {
            document.querySelectorAll('.video-item.hidden').forEach(item => item.classList.remove('hidden'));
            seeMoreVideosBtn.parentElement.style.display = 'none';
        });
    }
  </script>
</body>
</html>
