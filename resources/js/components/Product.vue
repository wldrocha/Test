<template>
  <v-app>
    <v-toolbar color="teal darken-2" dark scroll-target="#scrolling-techniques">
      <v-toolbar-title>Catálogo de Productos</v-toolbar-title>

      <v-spacer></v-spacer>
    </v-toolbar>

    <v-container>
      <v-layout wrap row justify-center>
        <v-flex class="pa-3" xs10 sm8 md5 lg3 v-for="product in productList" :key="product.id">
          
          <v-card class="elevation-5">
            <v-img :src="pathName + product.path"></v-img>
            <v-card-title class="primary-title">
              <h3 class="headline text-capitalize">{{ product.name }}</h3>
            </v-card-title>
            <v-card-text class="text-center">{{ product.description }}</v-card-text>
            <v-card-actions>
              <v-layout row align-end justify-end> 
               
                <v-btn  fab small right dark bottom color="red" @click="showModal('delete',product.id)" style="margin-right=5px">
                  <v-icon dark>delete</v-icon>
                </v-btn>
                <v-btn  fab small right  bottom color="primary" @click="showModal('edit',product.id)" style="margin-right=5px">
                  <v-icon dark>edit</v-icon>
              </v-btn>
              </v-layout>
            </v-card-actions>
          </v-card>
        </v-flex>
            <v-btn
              color="teal darken-2"
              dark
              absolute
              bottom
              fixed
              right
              fab
              @click="showModal('new')"
              class="mb-5"
            >
            <v-icon dark>add</v-icon>
            </v-btn>
            
            <v-dialog v-model="form" persistent max-width="600px" >
                <v-card v-if="type=='delete'">
                  <v-card-title>
                    <span class="headline red--text">¿Deseas Eliminar este producto?</span>
                  </v-card-title>
                  <v-card-actions mr-5>
                    <v-spacer></v-spacer>
                    <v-btn flat @click=" form = false">Cancelar</v-btn>
                    <v-btn color="red darken-2" flat @click="validate">Eliminar</v-btn>
                  </v-card-actions>
                </v-card>
                <v-card v-else>
                <form enctype="multipart/form-data">
                  <v-card-title>
                  <span class="display-1 teal--text">{{ textDialog }}</span>
                </v-card-title>
                <v-container grid-list-md>
                  <v-layout row wrap justify-center>
                    <!-- <upload-btn title="Toca para seleccionar tu imagen" class="white--text" color="teal darken-2" v-model="image">
                      <template slot="icon">
                          <v-icon>add</v-icon>
                        </template>
                      </upload-btn> -->
                      <input type="file" name="" id="" @change="imagen">
                    <v-flex xs9>
                      <v-text-field label="Nombre" hint="Nombre de tu producto" v-model="name" required></v-text-field>
                    </v-flex>
                    <v-flex xs9>
                      <v-textarea
                        label="Descripción"
                        hint="Coloca la descipción de tu Producto"
                        v-model="description"
                      ></v-textarea>
                    </v-flex>
                  </v-layout>
                </v-container>
                <v-card-actions mr-5>
                  <v-spacer></v-spacer>
                  <v-btn flat @click="form = false" >Cancelar</v-btn>
                  <v-btn color="teal darken-2" flat @click="validate">Guardar</v-btn>
                </v-card-actions>
                </form>
              </v-card>
            </v-dialog>
      </v-layout>
      <v-snackbar
        v-model="snackbar">
        {{ message }}
        <v-btn
          dark
          flat
          @click="snackbar = false"
        >
          Cerrar
        </v-btn>
      </v-snackbar>
    </v-container>
  </v-app>
</template>

<script>
import UploadButton from 'vuetify-upload-button'
import axios from 'axios';
const URL = 'http://127.0.0.1:8000/api/products';
// const PATH_NAME = window.location.origin + '/'; 
export default {
  created() {
    this.getProducts();
  },
  data() {
    return {
      form: false,
      id: '',
      name: '',
      description: '',
      img:'',
      textDialog: '',
      productList:[],
      snackbar: false,
      message: '',
      type: '',
      pathName: window.location.origin,
    }
  },
  methods:{
    imagen(e){
      let file =e.target.files[0];
      e.target.file = null;
      this.img= file;
    },
    getProducts(){
      axios.get(URL)
      .then(res => {
        this.productList = res.data;
      })
      .catch(err => console.log(err));
    },
    showModal(type, id = null){
      this.form = true;
      this.type = type;
      this.id = id;
      if(this.id){
        if(this.type == 'edit'){
          this.textDialog = 'Editar Producto';
          let product = this.productList.filter(e => e.id ==id);
          this.name = product[0].name;
          this.description = product[0].description
        }else{
          this.textDialog = '¿Deseas Eliminar este Producto?';
        }
      }else{
        this.textDialog = 'Agregar Producto';
      }  
    },
    validate(){
      if(this.type!='delete'){
        if(this.name === '' || this.description === ''){
        this.snackbar = true;
        this.message = 'Completa tus campos'
        return false
      }else{
        if(this.type == 'new'){
          this.newProduct();
        }else{         
          this.editProduct(this.id);
        }
      }
      }else{
          this.deleteProduct(this.id);
      }

    },
    newProduct () {
      let product = new FormData();
      product.append('name', this.name);
      product.append('description', this.description);
      product.append('image', this.img); 
      product.append('type', 'new');
      
      axios.post(URL,product)
      .then(res => {
        console.log(res);
        res = res.data;
          this.productList.push({
            id: res.product.id,
            name: res.product.name,
            description: res.product.description,
            path: res.image.path,
          })
          this.name= '';
          this.description= '';
          this.id = '';
          this.img = '';
          this.snackbar = true;
          this.message = res.message,
          this.form = false
        
      })
      .catch(err => console.log(err))
    },
    editProduct (id) { 
      // const product = {
      //   name: this.name,
      //   description: this.description
      // }
      let product = new FormData();
      product.append('id', id);
      product.append('name', this.name);
      product.append('description', this.description);
      product.append('image', this.img);
      product.append('type', 'update');
      product.append('_method', 'PUT');

      axios.post(URL+'/'+id,product)
      .then(res =>{
        this.productList = this.productList.filter((product) => {
          if(product.id == id){
            product.name = this.name;
            product.description = this.description;
            if(res.data.image){
              product.path = res.data.image.path;
              return product;
            }
            return product;
          }
        })
        this.name= '';
        this.description= '';
        this.id = '';
        this.snackbar = true;
        this.message = res.data.message;
        this.form = false
      })
      .catch( err => console.log(err))

    },
    deleteProduct (id){
      axios.delete(URL+'/'+id)
      .then( res => {
        this.productList = this.productList.filter( product => product.id != id);
        this.name= '';
        this.description= '';
        this.id = '';
        this.snackbar = true;
        this.message = res.data.message,
        this.form = false
      }).catch( err => {
        console.log(err);
      })
    }
  },
  components: {
      'upload-btn': UploadButton
    }
};
</script>

