<script type="text/javascript">
    /* <![CDATA[ */
    var root = '{$smarty.const.DOC_ROOT}';
    {literal}
    var a = document.getElementsByTagName("a");
    for(i = 0; i< a.length; i++)
        if(a[i].id != '')
            a[i].onclick = count_link;
    function count_link(event) {
        i = new Image();
        i.src= root+'/cl.php?id='+this.id;
        return true;
    }

    {/literal}
    /* ]]> */
</script>