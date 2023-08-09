<script setup>
    import { ref } from 'vue'
    const inputValue = ref('')
    const numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0]
    const operations = ['+', '-', '*', '/']
    const result = () => {
        inputValue.value = eval(inputValue.value)
    }
</script>

<template>
    <div class="flex">
      <input v-model="inputValue" type="text" class="input" placeholder="0">
      <ul class="list">
        <li v-for="num in numbers" :key="num" @click="inputValue += String(num)" class="list-item" >
          {{ num }}
        </li>
        <li v-for="op in operations" :key="op" @click="inputValue += String(op)" class="list-item op">
          {{ op }}
        </li>
        <li class="list-item op C" @click="inputValue = ''">
          C
        </li>
        <li class="list-item op res" @click="result">
          =
        </li>
      </ul>
    </div>
</template>

<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }
  
  ul {
    list-style: none;
  }
  
  .flex {
    width: 200px;
    margin: 0 auto;
    box-shadow: 0px 3px 15px grey;
  }
  
  .input {
    padding: 10px;
    text-align: right;
    color: #000;
    border-bottom: 1px solid black;
    width: 100%;
  }
  
  .input::placeholder {
    color: #000;
  }
  
  .list {
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    width: 100%;
  }
  
  .list-item {
    width: 33.3333333333%;
    text-align: center;
    padding: 10px 5px;
    color: #FFF;
    background-color: rgb(24, 0, 0);
    cursor: pointer;
    border-right: 1px solid #FFDFC4;
    border-bottom: 1px solid #FFDFC4;
  }
  
  .list-item:last-child {
    width: 100%;
  }
  
  .op {
    background-color: orangered;
  }
  
  .op:last-child {
    background-color: rgb(155, 43, 2);
  }
  
  .C {
    background-color: red;
  }
</style>