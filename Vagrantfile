# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  config.vm.define "dev" do |dev|
    dev.vm.box = "centos/7"

    dev.vm.provider "virtualbox" do |v|
      v.customize ["modifyvm", :id, "--memory", "4096"]
      v.customize ["modifyvm", :id, "--natdnshostresolver1", "on"]
      v.customize ["modifyvm", :id, "--natdnsproxy1", "on"]
  #    v.gui = true
    end

    dev.vm.network "private_network", ip: "192.168.56.101", hostsupdater: "skip"

    dev.vm.hostname = "i2ces.dev"
    dev.vm.boot_timeout = 2000
  end # end |dev|

  config.vm.define "qa", autostart: false do |qa|
    qa.vm.hostname = 'test.i2ces.info'
    # Alternatively, use provider.name below to set the Droplet name. qa.vm.hostname takes precedence.

    qa.vm.provider :digital_ocean do |provider, override|
      provider.image = '11483404'
      provider.ssh_key_name = 'Georgiana'
      override.ssh.private_key_path = '~/.ssh/id_rsa'
      override.vm.box = 'digital_ocean'
      override.vm.box_url = "https://github.com/smdahlen/vagrant-digitalocean/raw/master/box/digital_ocean.box"

      provider.token = '0700281c55c0d95e9d0c52ca93077d1268d46ae14bda34b8bb44b62c9d7f52f8'
      provider.image = 'centos-7-0-x64'
      provider.region = 'lon1'
      provider.size = '8gb'
    end
  end

  config.vm.define "staging", autostart: false do |staging|
    staging.vm.hostname = 'staging.i2ces.info'

    staging.vm.provider :digital_ocean do |provider, override|
      provider.image = '11478463'
      provider.ssh_key_name = 'Georgiana'
      override.ssh.private_key_path = '~/.ssh/id_rsa'
      override.vm.box = 'digital_ocean'
      override.vm.box_url = "https://github.com/smdahlen/vagrant-digitalocean/raw/master/box/digital_ocean.box"

      provider.token = '0700281c55c0d95e9d0c52ca93077d1268d46ae14bda34b8bb44b62c9d7f52f8'
      provider.image = 'centos-7-0-x64'
      provider.region = 'lon1'
      provider.size = '8gb'
    end
  end

  # skip syncing all code to vagrant user home by default
  config.vm.synced_folder ".", "/home/vagrant/sync", disabled: true

  config.vm.synced_folder "./backend", "/var/www/html/",
    mount_options: ['dmode=775','fmode=664'],
    type: "rsync",
    rsync__exclude: [".git/", "vendor/", "app/config/parameters.yml", "app/bootstrap.php.cache", "app/cache/sessions/", "composer.lock"],
    owner: "vagrant", group: "vagrant"

  config.vm.synced_folder "./frontend", "/var/www/html/frontend",
    mount_options: ['dmode=775','fmode=664'],
    type: "rsync",
    rsync__exclude: [".git/"],
    owner: "vagrant", group: "vagrant"

  config.vm.provision "ansible" do |ansible|
    ansible.playbook = "ansible/playbook.yml"

    # run commands as root
    ansible.sudo = true
  end
end
