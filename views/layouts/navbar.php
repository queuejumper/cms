

  <div class="col-xs-2">
<!--     <span class="glyphicon glyphicon-align-justify navbarToggle" aria-hidden="true"></span> -->
    <span class="logo">Company</span>
  </div>

  <div class="col-sm-4 col-xs-6 navbar-middle" >
    <div class="input-group">
      <input type="text" class="form-control search-input" placeholder="Search for">
      <span class="input-group-btn">
        <button class="btn search-btn" type="button">Go!</button>
      </span>
    </div>
    <div class="hint-area"></div>
  </div>
  <span class="col-sm-6 col-xs-4 navbar-right">
      <?php                       
          $this->beginContent('@app/views/layouts/_navbar.php'); 
          $this->endContent();
       ?>
  </span>
