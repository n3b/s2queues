# Symfony2 asynchronous queues bundle

just as example, how to use queues with any storage layer

you can use it to manage asynchronous processes like described here https://github.com/n3b/s2asynced

currently represented database and rabbitmq layers

 * by default database layer is enabled, so no need to configure it. you must only create db schema, according to symfony documentation

 * to enable rabbitmq layer:

   ```
   n3b_queues:
     adapter:
       rabbit:
         host:       localhost
         port:       55672
         user:       guest
         password:   guest
         vhost:      /
   ```

 * shared memory layer will be available soon
