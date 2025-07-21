<?php

function remove_query_param($param) {
    $url = parse_url($_SERVER['REQUEST_URI']);
    parse_str($url['query'] ?? '', $query_params);
    unset($query_params[$param]);
    
    $new_query = http_build_query($query_params);
    return $url['path'] . ($new_query ? '?' . $new_query : '');
}


function get_pagination_link($pagina) {
    $url = parse_url($_SERVER['REQUEST_URI']);
    parse_str($url['query'] ?? '', $query_params);
    
    $query_params['pagina'] = $pagina;
    $new_query = http_build_query($query_params);
    
    return $url['path'] . '?' . $new_query;
}