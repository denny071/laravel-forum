<template>
    <button :class="classes" @click="subscribe">订阅</button>
</template>

<script>
    export default {
        props: ['active'],

        computed: {
            classes() {
                return ['btn', this.active ? 'btn-primary' : 'btn-default'];
            }
        },

        methods:{
            subscribe(){
                axios.post(location.pathname + '/subscriptions');

                flash('Subscribed');
            }
        }
    }
</script>
