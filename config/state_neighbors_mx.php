<?php

return [
    'AGU' => ['ZAC', 'JAL'], // Aguascalientes
    'BC'  => ['BCS', 'SON'], // Baja California
    'BCS' => ['BC'], // Baja California Sur
    'CAM' => ['TAB', 'YUC', 'CHP'], // Campeche
    'CHH' => ['SON', 'SIN', 'DUR', 'COA'], // Chihuahua
    'CHP' => ['VER', 'OAX', 'TAB', 'CAM'], // Chiapas
    'COA' => ['CHH', 'DUR', 'ZAC', 'NLE'], // Coahuila
    'COL' => ['JAL', 'MIC'], // Colima
    'DUR' => ['CHH', 'SIN', 'NAY', 'ZAC', 'COA'], // Durango
    'GRO' => ['MOR', 'PUE', 'OAX', 'MIC'], // Guerrero
    'GTO' => ['ZAC', 'SLP', 'QRO', 'MIC', 'JAL'], // Guanajuato
    'HGO' => ['QRO', 'SLP', 'VER', 'PUE', 'TLX', 'MEX'], // Hidalgo
    'JAL' => ['NAY', 'ZAC', 'AGU', 'GTO', 'MIC', 'COL'], // Jalisco
    'MEX' => ['HGO', 'TLX', 'MOR', 'CDMX', 'MIC', 'GRO', 'PUE'], // Mexico State
    'MIC' => ['JAL', 'GTO', 'QRO', 'MEX', 'GRO', 'COL'], // Michoacán
    'MOR' => ['MEX', 'CDMX', 'PUE', 'GRO'], // Morelos
    'NAY' => ['DUR', 'ZAC', 'JAL'], // Nayarit
    'NLE' => ['COA', 'TAM', 'SLP', 'ZAC'], // Nuevo León
    'OAX' => ['PUE', 'VER', 'CHP', 'GRO'], // Oaxaca
    'PUE' => ['HGO', 'VER', 'OAX', 'MEX', 'TLX', 'MOR', 'GRO'], // Puebla
    'QRO' => ['SLP', 'HGO', 'MEX', 'MIC', 'GTO'], // Querétaro
    'QR'  => ['YUC', 'CAM'], // Quintana Roo
    'SLP' => ['ZAC', 'NLE', 'TAM', 'VER', 'HGO', 'QRO', 'GTO'], // San Luis Potosí
    'SIN' => ['CHH', 'DUR', 'NAY'], // Sinaloa
    'SON' => ['BC', 'CHH', 'SIN'], // Sonora
    'TAB' => ['CHP', 'CAM', 'VER'], // Tabasco
    'TAM' => ['NLE', 'SLP', 'VER'], // Tamaulipas
    'TLX' => ['PUE', 'MEX', 'HGO'], // Tlaxcala
    'VER' => ['TAM', 'SLP', 'HGO', 'PUE', 'OAX', 'CHP', 'TAB'], // Veracruz
    'YUC' => ['CAM', 'QR'], // Yucatán
    'ZAC' => ['DUR', 'COA', 'NLE', 'SLP', 'GTO', 'JAL', 'AGU'], // Zacatecas
    'CDMX' => ['MEX', 'MOR', 'PUE', 'TLX'], // Mexico City
    'CMX' => ['MEX', 'MOR', 'PUE', 'TLX'], // Alias for Ciudad de México

];
