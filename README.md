## Prerequisites

* Vagrant
* Ansible

### Prepare provisioner for usage

```bash
➤ sudo ansible-galaxy install geerlingguy.apache
    geerlingguy.mysql geerlingguy.php geerlingguy.git
    geerlingguy.composer --force
```

### Start VM

```bash
➤ vagrant up
```

### [Manual] Install dependencies

```bash
➤ vagrant ssh
$ cd /var/www/html
$ composer install
```

## Deploy to DigitalOcean

```bash
vagrant plugin install vagrant-digitalocean
vagrant up --provider=digital_ocean
```
