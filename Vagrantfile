# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.

VAGRANTFILE_API_VERSION = "2"

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://atlas.hashicorp.com/search.
  config.vm.box = "centos/7"

  config.vm.provider "virtualbox" do |v|
    v.customize ["modifyvm", :id, "--memory", "3072"]
#    v.gui = true
  end

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  # config.vm.network "forwarded_port", guest: 80, host: 8080

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  config.vm.network "private_network", ip: "192.168.56.101"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  # config.vm.network "public_network"

  config.vm.hostname = "i2ces.dev"
  config.vm.boot_timeout = 2000

  config.vm.synced_folder "./backend", "/var/www/html/",
    mount_options: ['dmode=775','fmode=664'],
    type: "rsync",
    rsync__exclude: ".git/",
    owner: "vagrant", group: "vagrant"

  config.vm.provision "ansible" do |ansible|
    ansible.playbook = "ansible/playbook.yml"

    # run commands as root
    ansible.sudo = true
  end
end

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|
  config.vm.hostname = 'test.i2ces.com'
  # Alternatively, use provider.name below to set the Droplet name. config.vm.hostname takes precedence.

  config.vm.provider :digital_ocean do |provider, override|
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
