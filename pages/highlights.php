  <style>
      .container {
          padding: 40px 20px;
      }

      h2 {
          text-align: center;
          font-size: 32px;
          margin-bottom: 40px;
      }

      .highlights-grid {
          display: grid;
          grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
          gap: 24px;
      }

      .highlight-card {
          background-color: #fff;
          border-radius: 10px;
          overflow: hidden;
          box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
          transition: transform 0.3s ease;
      }

      .highlight-card:hover {
          transform: translateY(-5px);
      }

      .video-wrapper {
          position: relative;
          width: 100%;
          padding-bottom: 56.25%;
          /* 16:9 ratio */
          background: #000;
      }

      .video-wrapper iframe,
      .video-wrapper video {
          position: absolute;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          border: none;
      }

      .highlight-text {
          padding: 20px;
      }

      .highlight-title {
          font-size: 20px;
          font-weight: bold;
          margin-bottom: 10px;
          color: #222;
      }

      .highlight-desc {
          font-size: 15px;
          color: #555;
          line-height: 1.5;
      }

      @media (max-width: 600px) {
          .highlight-title {
              font-size: 18px;
          }

          .highlight-desc {
              font-size: 14px;
          }
      }
  </style>

  <div class="container">
      <h2>🌍 Destination Highlights</h2>

      <div class="highlights-grid">

          <!-- Highlight Card 1 -->
          <div class="highlight-card">
              <div class="video-wrapper">
                  <iframe src="https://www.youtube.com/embed/Scxs7L0vhZ4" allowfullscreen></iframe>
              </div>
              <div class="highlight-text">
                  <div class="highlight-title">Paris, France</div>
                  <div class="highlight-desc">Enjoy a romantic 7-day trip through the Eiffel Tower, museums, and cozy cafes of Paris.</div>
              </div>
          </div>

          <!-- Highlight Card 2 -->
          <div class="highlight-card">
              <div class="video-wrapper">
                  <video src="videos/tokyo.mp4" controls></video>
              </div>
              <div class="highlight-text">
                  <div class="highlight-title">Tokyo, Japan</div>
                  <div class="highlight-desc">Experience the blend of futuristic vibes and traditional culture in this 12-day Japan tour.</div>
              </div>
          </div>

          <!-- Highlight Card 3 -->
          <div class="highlight-card">
              <div class="video-wrapper">
                  <iframe src="https://www.youtube.com/embed/vtpk6n2nH8A" allowfullscreen></iframe>
              </div>
              <div class="highlight-text">
                  <div class="highlight-title">New York City</div>
                  <div class="highlight-desc">Discover the vibrant life of NYC with Times Square, Central Park, and Broadway in 9 days.</div>
              </div>
          </div>

      </div>
  </div>