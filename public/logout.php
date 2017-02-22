<?php

setCookie("token", "", time()-1000, "/");
header("Location: /login/");
