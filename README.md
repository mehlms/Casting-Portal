# CHAPMAN CASTING

Things that were NOT completed

- Ability to remove talent from email blast (Top Priority)
- Scripts/Posters are not yet deleted from server when call is closed, profile photos are removed
- Calls have no expiration date, they remain open until closed by collaborator
- Login not hooked up to official Chapman API
- You cannot sort calls in 'calls.php', sorted by 'date added' by default
- No stored procedures yet
- No admin page yet, to remove students, add genres, add classes
- Notification button does not glow red, or display number of notifications since last check
- Anything else you remember from class? No AI stuff yet either.

Required software

- PHP-FPM 5.6
- Latest NGINX
- MySQL

OTHER NOTES / TO DO'S

- /inc/email.php is for altering email server credentials (PLEASE URGENT we need to use Chapman no_reply email. Everything else costs additional $$)
- /inc/db.php is for altering database connection
- Enable read/write permissions to all users to all folders within /public/resources/assets/
- (RECOMMENDED) Change number of created processes/worker_processes in all above software config files to match number of cores on machine. Worker connections is (numCores * 1024)
- php-fpm needs to run on 9000, I think that's what it is by default.
- Replace nginx.conf with installed nginx.config, otherwise nothing will work!
- Change directory path in nginx.conf to point to /public directory on new machine

DIRECTORY STRUCTURE

- /inc and /public need to be beside each other
- keep everything inside /public as is
