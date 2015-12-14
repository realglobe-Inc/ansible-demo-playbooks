# WordPress構築Playbook - PC上での手順

## 目次

### Ansibleを実行する

1. VMを作成する
2. Ansible実行マシンにSSH接続する
3. 実行マシン上にAnsibleをインストール
4. Playbookをダウンロード
5. WordpressサーバーへのSSH接続設定
6. インベントリの作成
7. ansible-playbookを実行

### playbookを編集する

1. DBサーバーを作成する
  * 新しくDB用サーバーのVMを作成する
  * コントロールマシンからSSH接続できることを確認する
2. インベントリにグループを追加する
3. playbookを編集する
  * site.ymlを編集する
  * mysql roleを編集する
  * wordpress roleを編集する

# Ansibleを実行する

コントロールマシンにSSHログインし、Ansibleをインストールしてplaybookを実行する

## 1. VMを作成する

* Ansible実行マシン用VMを作成する
  * イメージは「Amazon Linux AMI」を使用
  * セキュリティグループの設定で22(SSH)番ポートを解放
  * 仮想マシンの Name タグは「ansible-machine-<<ユーザー名>>」

* Wordpressサーバー用VMを作成する
  * イメージは「CentOS 6 (x86_64) - with Updates HVM」を使用
  * セキュリティグループの設定で22(SSH), 80(HTTP)番ポートを解放
  * 仮想マシンの Name タグは「wordpress-server-<<ユーザー名>>」

* データベースサーバー用VMを作成する
  * イメージは「CentOS 7 (x86_64) - with Updates HVM」を使用
  * セキュリティグループの設定で22(SSH), 3306(MySQL)番ポートを解放
  * 仮想マシンの Name タグは「wordpress-db-<<ユーザー名>>」


:warning: 詳細な手順は別途配布のAWS上の手順を参照

## 2. Ansible実行マシンにSSH接続する

#### :small_blue_diamond: Mac、Linuxの場合

1. ターミナルを開く
2. VM作成時に使った秘密鍵のパーミッションを600に変更する

  ```chmod 600 ssh_private_key```

3. Ansible実行マシンにSSH接続する

  ```ssh ec2-user@xxx.xxx.xxx.xxx -i ssh_private_key```

#### :small_blue_diamond: Windowsの場合（Tera term使用時）

1. Tera termを起動する
2. ホストにはAnsible実行マシンのIPアドレスを入力
3. ユーザー名は「root」、秘密鍵にはVM作成時に使った秘密鍵を指定

## 3．実行マシン上にAnsibleをインストール

### yumでAnsibleをインストールする

1. エディタをインストールする
  * vimやemacsに不慣れな方には nano がオススメです

  ```sh
  # 事前に nano の設定を追加します
  echo 'set const' >> ~/.nanorc
  ```

2. Git をインストールする
  * プレイブック取得時に git を使うのでインストールします
  ```sh
  sudo yum install git -y
  ```

3. Ansibleをインストール

  ```sudo pip install ansible```

4. ansibleコマンドが使用できることを確認する

  ```sh
  ansible[Tab][Tab]
  ansible    ansible-doc    ...    ansible-vault
  ```

## 4．playbookをダウンロード

今回のハンズオンで使用するplaybookをダウンロードする

1. GitHubの公開リポジトリからgit cloneする

  ```git clone https://github.com/realglobe-Inc/ansible-demo-playbooks.git```

2. cdコマンドを使用しディレクトリを移動する

  ```cd ansible-demo-playbooks/wordpress-nginx/```

## 5. WordpressサーバーへのSSH接続設定

Ansible実行マシンからWordpressサーバーにSSH接続できるようにする

1. VM作成時に指定した秘密鍵をAnsible実行マシンにコピペする

  ```nano ~/.ssh/id_rsa```

2. 秘密鍵のパーミッションを600に変更する

  ```chmod 600 ~/.ssh/id_rsa```

3. sshでWordpressサーバーに接続できることを確認する

  ```ssh centos@xxx.xxx.xxx.xxx```

4. Wordpressサーバーからログアウトする

  ``` exit ```

## 6. インベントリ作成

今回は`hosts`という名前でインベントリファイルを作成する

1. リポジトリ内に`hosts.example`というファイルがあるので、これをコピーして使用する

  ```cp hosts.example hosts```

2. エディタで`hosts`を開く

  ```nano hosts```

3. 以下のように書き換える（xxx.xxx.xxx.xxxはWordpressサーバーのIPアドレス）

  ```ini
  [wordpress-server]
  xxx.xxx.xxx.xxx ansible_ssh_user=centos
  ```

## 7. ansible-playbook実行

ansible-playbookコマンドを実行する

1. wordpressをインストールするplaybookを実行

  ```ansible-playbook site.yml -i hosts```

2. ブラウザからアクセスして、Wordpressインストール画面が表示されることを確認

3. wordpressをアンインストールするplaybookを実行

  ```ansible-playbook clean.yml -i hosts```

4. Wordpressにアクセスできなくなっていることを確認

# playbookを編集する

先ほど構築したWordpressサーバーは、WebアプリケーションとDBが同じホスト上に乗っている一台構成のものでしたが、プロダクション運用の際にはパフォーマンスや可用性を考慮してアプリケーション・サーバーとDBサーバーを分割した構成を取ることも多いです。  
しかし、残念ながら（？）今のままのplaybookはこの様な構成に対応していません。  
そこで、ここからはplaybookを編集してアプリ+DBの二台構成Wordpress環境を構築してみましょう！

## 8. DBサーバーの接続確認

* Ansible実行マシンから DBサーバー用VMにssh接続できることを確認する
  * Wordpressサーバーと同じSSH Keyを使用しているので、設定の変更なしにSSH接続できる

    ```sh
    ssh centos@xxx.xxx.xxx.xxx
    exit
    ```

## 9. インベントリにグループを追加する

インベントリを書き換え、wordpress-dbグループを追加する

* エディタで`hosts`を開き、以下のように書き換える

  ```ini
  [wordpress-server]
  xxx.xxx.xxx.xxx ansible_ssh_user=centos

  [wordpress-db]
  xxx.xxx.xxx.xxx ansible_ssh_user=centos
  ```

## 10. playbookを編集する

### site.ymlを編集し、DBサーバーの設定手順、アプリケーション・サーバーの設定手順を分割する

DBサーバー、アプリケーション・サーバー毎に必要なロールが異なることに注意してください。

* site.yml

  ```yaml
  ---
  - name: 最初にDB, アプリ両サーバーの情報を取得する
    hosts: all

  - name: MySQLをインストール
    hosts: wordpress-db
    sudo: yes
    roles:
      - common
      - mysql

  - name: Wordpress, Nginx, PHP-FPMをインストール
    hosts: wordpress-server
    sudo: yes
    roles:
      - common
      - nginx
      - php-fpm
      - wordpress
  ```

### mysql操作系のタスクの実行場所を移動する

元のプレイブックでは、DBもアプリケーションも同じサーバー内に存在したため、Wordpressが使うデータベースと接続ユーザーの設定を`wordpress`ロール内で実行していました。  
このままだと、DBサーバーとアプリケーション・サーバーが分かれた時に設定ができなくなってしまうので、これらの操作を`mysql`ロールに移しましょう。

* 以下2タスクを`roles/wordpress/tasks/main.yml`から`roles/mysql/tasks/main.yml`の最後尾に移動する

  ```yaml
  - name: Wordpress用データベースを作成
    mysql_db:
      name: "{{ wp_db_name }}"
      state: present

  - name: Wordpress用データベースユーザーを作成
    mysql_user:
      name: "{{ wp_db_user }}"
      password: "{{ wp_db_password }}"
      priv: "{{ wp_db_name }}.*:ALL"
      host: localhost
      state: present
  ```

### DBがアプリケーション・サーバーからの接続を受け入れる様にする

上で移動した2タスクの内、`Wordpress用データベースユーザーを作成`タスクをよく見てみると、`host: localhost`と書いてあります。  
これはデータベース・ユーザーの接続元ホストを制限するための設定なので、アプリケーション・サーバーからの接続を受け入れるように書き換える必要があります。

* `roles/mysql/tasks/main.yml`の最後のタスクを書き換え

  ```yaml
  - name: Wordpress用データベースユーザーを作成
    mysql_user:
      name: "{{ wp_db_user }}"
      password: "{{ wp_db_password }}"
      priv: "{{ wp_db_name }}.*:ALL"
      host: "{{ hostvars[item].ansible_default_ipv4.address }}"
      state: present
    with_items: groups['wordpress-server']
  ```

  長い書き方になってしまいますが、この様に書くことで「`wordpress-server`グループに属する全ホストのipアドレス」を動的に設定することができ、冗長化などの際のplaybookの汎用性を高めることができます。
  なお、`with_items`はplaybook内のタスクをループさせる時に使う書き方です。

### WordpressからDBサーバーに接続できる様にする

Wordpressの設定ファイル`wp-config.php`の中にDBのホスト名の設定がありますが、ここも`localhost`になっているので修正しましょう。  
`Wordpress用データベースユーザーを作成`の修正と同様の書き方ができますが、今度はDBサーバーのipを取りたいので`wordpress-db`グループの設定を取得します。  
また、DBサーバーについてはアプリケーション・サーバーの様に単純に全ホストの値をループで設定することはできませんので、今回はDBサーバーは1台である前提とします。

* `roles/wordpress/templates/wp-config.php`を修正する

  ```php
  /** MySQL hostname */
define('DB_HOST', 'localhost');
  ```

  を、以下の様に書き換え

  ```php
  /** MySQL hostname */
  define('DB_HOST', '{{ hostvars[groups['wordpress-db'][0]].ansible_default_ipv4.address }}');
  ```

### ansible-playbookを実行して、ブラウザからアクセスし、wordpressをセットアップする

```ansible-playbook site.yml -i hosts```

これでDBサーバー、アプリケーション・サーバーの2台構成のWordpress環境の完成です！ 
