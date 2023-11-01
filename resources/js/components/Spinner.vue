<template>
    <div class="spinner-wrapper">
        <div class="loading">
            <div></div>
            <div></div>
            <div></div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {};
    }
}
</script>

<style scoped>
.spinner-wrapper {
    position: relative;
    height: 4rem;
}
.loading {
    display: -webkit-box;
    display: flex;
    -webkit-box-pack: center;
    justify-content: center;
}
.loading div {
    width: 1rem;
    height: 1rem;
    margin: 2rem 0.3rem;
    background: #979fd0;
    border-radius: 50%;
    -webkit-animation: 0.7s bounce infinite alternate;
    animation: 0.7s bounce infinite alternate;
}
.loading div:nth-child(2) {
    -webkit-animation-delay: 0.3s;
    animation-delay: 0.3s;
}
.loading div:nth-child(3) {
    -webkit-animation-delay: 0.6s;
    animation-delay: 0.6s;
}
@-webkit-keyframes bounce {
    to {
        opacity: 0.3;
        -webkit-transform: translate3d(0, -1rem, 0);
        transform: translate3d(0, -1rem, 0);
    }
}
@keyframes bounce {
    to {
        opacity: 0.3;
        -webkit-transform: translate3d(0, -1rem, 0);
        transform: translate3d(0, -1rem, 0);
    }
}
.donut {
    width: 2rem;
    height: 2rem;
    margin: 2rem;
    border-radius: 50%;
    border: 0.3rem solid rgba(151, 159, 208, 0.3);
    border-top-color: #979fd0;
    -webkit-animation: 1.5s spin infinite linear;
    animation: 1.5s spin infinite linear;
}
.donut.multi {
    border-bottom-color: #979fd0;
}
</style>
