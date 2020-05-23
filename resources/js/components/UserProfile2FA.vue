<template>
    <div>
    <label class="form-item-textnode">
        <input type="checkbox" name="two_factor_state" v-model="two_factor_state" :checked="state">
    </label><br>

    <div v-if="two_factor_state">
        <label class="form-item-textnode">
            <input type="radio" name="two_factor_method" value="email" :checked="method == 'email'">
            Email (send code to email)
        </label>
    </div>
    </div>
</template>

<script>
    export default {
        props: [
            'state',
            'method',
        ],
        data: function () {
            return {
                // two_factor_state: document.getElementById('two_factor_state').checked,
                two_factor_state: this.state,
            }
        },
        mounted() {
            // console.log('Component mounted.')
        }
    }
</script>
