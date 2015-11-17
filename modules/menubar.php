<?php $token =  $_SESSION['token']; ?>

<nav class="navbar navbar-default" style="margin-bottom:0px;">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
      <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
        <span class="sr-only">Toggle navigation</span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </button>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav nav-justified">
        <li><a href="welcome.php?token=<?php echo $token ?>">Overview</a></li>
        <li><a href="statement.php?token=<?php echo $token ?>">Account Statement</a></li>
        <li><a href="transaction.php?token=<?php echo $token ?>">Money Transfer</a></li>
        <li><a href="#">Finance</a></li>
        <li><a href="branch.php?token=<?php echo $token ?>">Branches and ATM</a></li>
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>

