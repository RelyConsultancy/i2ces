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

## Update Continuous Integration Environment

```bash
➤ ansible-playbook ansible/cd.yml -i ansible/hosts -vvv -u root
```

You can validate it is running by loading http://ci.i2ces.info:8080/ in your browser.
