## Prerequisites

* Vagrant
* Ansible

### Prepare provisioner for usage

```bash
➤ sudo ansible-galaxy install geerlingguy.repo-remi geerlingguy.apache geerlingguy.mysql geerlingguy.php geerlingguy.git geerlingguy.composer --force
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

# raise machine
vagrant up qa
vagrant up staging

# recreate with same IP
vagrant rebuild qa
vagrant rebuild staging

# reapply provisioning
vagrant provision qa
vagrant provision staging
```

Execute `ansible/playbook.yml` tasks.
```bash
➤ ansible-playbook ansible/playbook.yml -i ansible/hosts -vvvv -u root
```
