<?php
?>

<head>
    <meta charset="UTF-8">
    <title>Impressum Link Zeile</title>
    <style>
        .impressum-footer {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            color: white;
            position: relative;
            bottom: 0;
        }

        .impressum-footer .buttons {
            display: flex;
            justify-content: center;
        }

        .impressum-footer p {
            padding: 20px;
            cursor: pointer;
        }
    </style>
</head>

<div class="impressum-footer">
    <div class="buttons">
        <p onclick="load_Impressum()">Impressum</p>
        <p onclick="load_Datenschutz()">Datenschutzrichtlinien</p>
    </div>
</div>