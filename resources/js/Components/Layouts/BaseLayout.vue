<template>
    <div>
        <slot />
    </div>
</template>

<script>
import { Row, Col } from 'vant'

export default {
    components: {
        [Row.name]: Row,
        [Col.name]: Col
    }
}
</script>
