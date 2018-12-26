<template>
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="cart-header">
                    SignUp
                </div>
                <div class="card-body">
                    <b-form @submit="signup">
                        <b-form-group label="Email address" label-for="signupEmail">
                            <b-form-imput id="signupEmail" type="email" v-model="form.email" required></b-form-imput>
                        </b-form-group>
                        <b-form-group label="Password" label-for="signupPassword">
                            <b-form-input id="signupPassword" type="password" v-model="form.password" required></b-form-input>
                        </b-form-group>
                        <b-button type="submit" variant="primary">Sign Up</b-button>
                    </b-form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                form: {
                    email: null,
                    password: null,
                }
            }
        },
        methods: {
            login(event) {
                event.preventDefault();
                alert('Signup with ' + this.$data.from.email);
            }
        }
    }
</script>

<style scoped>

</style>