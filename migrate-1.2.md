# Migration to 1.2

  - remove the deploy info reading from bootstrap
  - remove the phpunit constant reading from bootstrap
  - You can call registerGlobal() to register the global vars for root directory and container

changes:
  - getDoctrineContainer() is removed from BootContainer