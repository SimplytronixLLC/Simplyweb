<template>

<div class="min-h-screen flex justify-center items-center">

<div class="w-96 bg-white p-6 rounded shadow">

<h2 class="text-2xl mb-5">
Supplier Login
</h2>

<input
v-model="email"
placeholder="Email"
class="border w-full p-2 mb-3"
/>

<input
v-model="password"
type="password"
placeholder="Password"
class="border w-full p-2 mb-3"
/>

<button
@click="login"
class="bg-blue-600 text-white p-2 w-full"
>

Login

</button>

</div>

</div>

</template>

<script setup>

import axios from 'axios'
import {ref} from 'vue'
import {useRouter} from 'vue-router'

const router=useRouter()

const email=ref('')
const password=ref('')

async function login(){

const response=
await axios.post(
'/api/supplier/login',
{
email:email.value,
password:password.value
}
)

localStorage.setItem(
'token',
response.data.token
)

router.push(
'/supplier/dashboard'
)

}

</script>