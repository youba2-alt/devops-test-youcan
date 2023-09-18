# Challenge 1 : Save our Media layer from folders overflow

<details>
<summary>Click to check the challenge details</summary>

## Challnge-1 details

### Context
The Media layer (called cdn - even it's not a real cdn) is composed by an app that generates and serves multiple sizes on the fly.
In first request, the app fetch the original media, generates 3 sizes (sm, md, lg), and stores them in order to serve them directly from the disk in the future.     

### Problematic
It's common that linux folders have a limit of sub-folder they can hold. 
Our internal app can save files under a custom path, but we need a custom vhost that can serve those files from that custom path.

### Expected resolution
Using ansible and docker-compose, pop-up 2 containers distributed as following:

1. 1 x Openresty container (this will contain your resolution)
2. 1 x PHP container that hosts your sample.

***Resolution example*** 

A request can ask to serve a file under the following path: `/stores/XYZ20211008ABC/categorie/image.png`

we need a vhost that can reformulate this request to the following: `/stores/XY/Z2/XYZ20211008ABC/categorie/image.png`

### Hints
Nginx doesn't support lua scripting so you can manipulate the coming request as you want, here we can introduce - lua scripting - a scripting module that is added in top of nginx to allow customized scripting.

<br />

[👉 more details](https://github.com/youcan-shop/coding-challenges/blob/master/DevOps%20Engineer/README.md#coding-challenge-i-save-our-media-layer-from-folders-overflow)

</details>

---

## Requirements : 

* having docker and docker compose installed
* having ansible installed
* having python3 


## Notes

* supported images types : jpeg/jpg, png, bmp
* in ansible `become: true`, is needed to install pip3, you can check that out for your case.

## Usage

* Run ansible script : `sudo ansible-playbook ansible/main.yaml`. 
* set domain name on Hosts file (127.0.0.1 example.com) 

