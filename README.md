# formula1app

// TECHOLOGIES

- Composer 
- Yarn 
- Docker 


// INSTALLATAION IN DEV MODE

- git clone https://github.com/Ylahjaily/formula1app
- cd formula1app
- docker-compose up -d 
- docker exec web bin bash 
  -> php bin/console composer install
  -> php bin/console doctrine:database:create
  -> php bin/console d:s:u --force 
  
- yarn run encore dev --watch 

=> Run on localhost:88
