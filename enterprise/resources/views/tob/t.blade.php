<script>
if (self != top) {
     parent.payok();
} else {
    location.href = '/pay/html/{{ $merchantid }}/{{ $terminal_sn }}/{{ $appid }}?product_id={{ $productId }}';
}
</script>