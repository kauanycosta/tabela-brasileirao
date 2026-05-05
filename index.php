<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brasileirão Série A</title>
    <link rel="shortcut icon" type="image/png"
        href="https://files.clicrdc.com.br/wp-content/uploads/2025/04/WhatsApp-Image-2025-04-14-at-14.04.25.jpeg" />
    <link rel="stylesheet" href="style.css">
</head>

<?php

$temporada = isset($_GET['temporada']) ? $_GET['temporada'] : '2025';

$ch = curl_init();

$urlApi = 'https://jsuol.com.br/c/monaco/utils/gestor/commons.js?file=commons.uol.com.br/sistemas/esporte/modalidades/futebol/campeonatos/dados/' . $temporada . '/30/dados.json';

curl_setopt($ch, CURLOPT_URL, $urlApi);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$resultApi = curl_exec($ch);

//var_dump($resultApi);
//exit;

$arrayJsonApi = json_decode($resultApi, true);

$arrayEquipes = $arrayJsonApi['equipes'];
$fase = $arrayJsonApi['ordem-fases'][0];
$arrayClassificacao = $arrayJsonApi['fases'][$fase]['classificacao']['grupo']['Único'];
?>

<body>

    <header class="top-header">
        <h1>Brasileirão série A</h1>
        <h2>CLASSIFICAÇÃO</h2>
    </header>

    <main class="tabela-container">
        <section class="temporada-filtro">
            <label for="ano-temporada">Temporada</label>
            <select id="ano-temporada">
                <option value="2025" <?= $temporada == '2025' ? 'selected' : '' ?>>2025</option>
            </select>
        </section>

        <script>
            document.getElementById('ano-temporada').addEventListener('change', function () {
                const temporada = this.value;
                window.location.href = '?temporada=' + temporada;
            });
        </script>

        <table class="tabela-classificacao">
            <thead>
                <tr>
                    <th style="width: 45%">Clube</th>
                    <th style="font-weight: bold;">Pts</th>
                    <th>PJ</th>
                    <th>VIT</th>
                    <th>E</th>
                    <th>DER</th>
                    <th>GM</th>
                    <th>GC</th>
                    <th>SG</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $ordemClassificacao = 0;
                foreach ($arrayClassificacao as $keyClassificacao => $valueClassificacao) {
                    $ordemClassificacao++;
                    ?>
                    <tr class="labels-tabela">
                        <?php
                        if ($ordemClassificacao >= 1 and $ordemClassificacao <= 4) {
                            $background = "#4285f4";
                        } else if ($ordemClassificacao == 5 or $ordemClassificacao == 6) {
                            $background = "#fa7b17";
                        } else if ($ordemClassificacao >= 7 and $ordemClassificacao <= 12) {
                            $background = "#34a853";
                        } else if ($ordemClassificacao >= 17 and $ordemClassificacao <= 20) {
                            $background = "red";
                        } else {
                            $background = "white";
                        }
                        ?>
                        <td class="coluna-clube" style="border-left: 3px solid <?= $background ?>;">
                            <?= $ordemClassificacao ?>
                            <img src="<?= $arrayEquipes[$valueClassificacao]['brasao'] ?>"
                                style="max-width: 24px; max-height: 24px; vertical-align: middle;">
                            <?= $arrayEquipes[$valueClassificacao]['nome-comum'] ?>
                        </td>
                        <td class="colunas" style="font-weight: bold;">
                            <?= $pontos = $arrayJsonApi['fases'][$fase]['classificacao']['equipe'][$valueClassificacao]['pg']['total']; ?>
                        </td>
                        <td class="colunas">
                            <?= $partidasJogadas = $arrayJsonApi['fases'][$fase]['classificacao']['equipe'][$valueClassificacao]['j']['total']; ?>
                        </td>
                        <td class="colunas">
                            <?= $partidasGanhas = $arrayJsonApi['fases'][$fase]['classificacao']['equipe'][$valueClassificacao]['v']['total']; ?>
                        </td>
                        <td class="colunas">
                            <?= $partidasEmapatadas = $arrayJsonApi['fases'][$fase]['classificacao']['equipe'][$valueClassificacao]['e']['total']; ?>
                        </td>
                        <td class="colunas">
                            <?= $partidasPerdidas = $arrayJsonApi['fases'][$fase]['classificacao']['equipe'][$valueClassificacao]['d']['total']; ?>
                        </td>
                        <td class="colunas">
                            <?= $golsMarcados = $arrayJsonApi['fases'][$fase]['classificacao']['equipe'][$valueClassificacao]['gp']['total']; ?>
                        </td>
                        <td class="colunas">
                            <?= $golsTomados = $arrayJsonApi['fases'][$fase]['classificacao']['equipe'][$valueClassificacao]['gc']['total']; ?>
                        </td>
                        <td class="colunas">
                            <?= $saldoGols = $arrayJsonApi['fases'][$fase]['classificacao']['equipe'][$valueClassificacao]['sg']['total']; ?>
                        </td>
                        <!-- coluna escondida, mas com informações usadas no card -->
                        <td class="colunas coluna-escondida">
                            <?php
                            $codTime = $arrayEquipes[$valueClassificacao]['id'];
                            $arrayJogos = $arrayJsonApi['fases'][$fase]['jogos']['data'];
                            $dataHoje = date('Y-m-d');

                            $posicaoRodada = 0;
                            $t = 0;
                            $arrayDatas = [];

                            foreach ($arrayJogos as $keyDatas => $valueDatas) {
                                $t++;
                                $arrayDatas[$t] = $keyDatas;

                                if ($keyDatas < $dataHoje) {
                                    $arrayMenor = $keyDatas;
                                }
                            }

                            for ($i = 1; $i < $t; $i++) {
                                if ($arrayDatas[$i] == $arrayMenor) {
                                    $posicaoRodada = $i;
                                }
                            }

                            $count = 0;
                            $partidasExibidas = 0;
                            $resultados = [];

                            do {
                                $arrayMenor = $arrayDatas[$posicaoRodada - $count];
                                if ($arrayMenor === "0000-00-00") {
                                    $count++;
                                    continue;
                                }

                                $arrayPartidasDatas = $arrayJsonApi['fases'][$fase]['jogos']['data'][$arrayMenor] ?? [];

                                if (!is_array($arrayPartidasDatas) || empty($arrayPartidasDatas)) {
                                    $count++;
                                    continue;
                                }

                                $qtdePartidas = count($arrayPartidasDatas);

                                $resposta = 2;

                                for ($i = 0; $i < $qtdePartidas; $i++) {
                                    $time1 = $arrayJsonApi['fases'][$fase]['jogos']['id'][$arrayPartidasDatas[$i]]['time1'];
                                    $time2 = $arrayJsonApi['fases'][$fase]['jogos']['id'][$arrayPartidasDatas[$i]]['time2'];

                                    if ($codTime == $time1 || $codTime == $time2) {
                                        $resposta = 1;
                                        $partidaID = $arrayPartidasDatas[$i];
                                        $mandante = ($codTime == $time1) ? 1 : 0;

                                        $placar1 = $arrayJsonApi['fases'][$fase]['jogos']['id'][$partidaID]['placar1'];
                                        $placar2 = $arrayJsonApi['fases'][$fase]['jogos']['id'][$partidaID]['placar2'];

                                        if ($placar1 === null || $placar2 === null) {
                                            $resultadoFinal = "assets/img/em_andamento.gif";
                                        } else {
                                            if ($mandante == 1) {
                                                if ($placar1 > $placar2) {
                                                    $resultadoFinal = "assets/img/icon_vitoria.png";
                                                } elseif ($placar1 < $placar2) {
                                                    $resultadoFinal = "assets/img/icon_derrota.png";
                                                } else {
                                                    $resultadoFinal = "assets/img/icon_empate.png";
                                                }
                                            } else {
                                                if ($placar2 > $placar1) {
                                                    $resultadoFinal = "assets/img/icon_vitoria.png";
                                                } elseif ($placar2 < $placar1) {
                                                    $resultadoFinal = "assets/img/icon_derrota.png";
                                                } else {
                                                    $resultadoFinal = "assets/img/icon_empate.png";
                                                }
                                            }
                                        }
                                        $resultados[] = $resultadoFinal;
                                        $partidasExibidas++;

                                        if ($partidasExibidas >= 5) {
                                            break 2;
                                        }
                                    }
                                }
                                $count++;
                            } while (($posicaoRodada - $count) > 0);
                            ;
                            echo "<div class='ultimos-resultados'>";
                            foreach ($resultados as $resultado) {
                                echo "<img src='$resultado' style='max-width: 20px; max-height: 20px; margin-right: 5px;'>";
                            }
                            echo "</div>";
                            $proximoJogo = null;

                            foreach ($arrayDatas as $data) {
                                if ($data > $dataHoje) {
                                    $proximasPartidas = $arrayJsonApi['fases'][$fase]['jogos']['data'][$data];
                                    foreach ($proximasPartidas as $partidaID) {
                                        $time1 = $arrayJsonApi['fases'][$fase]['jogos']['id'][$partidaID]['time1'];
                                        $time2 = $arrayJsonApi['fases'][$fase]['jogos']['id'][$partidaID]['time2'];

                                        if ($codTime == $time1 || $codTime == $time2) {
                                            $nomeTime1 = $arrayEquipes[$time1]['nome-comum'];
                                            $nomeTime2 = $arrayEquipes[$time2]['nome-comum'];
                                            $horario = $arrayJsonApi['fases'][$fase]['jogos']['id'][$partidaID]['horario'];
                                            $proximoJogo = "$brasaoTime1 $nomeTime1 x $brasaoTime2 $nomeTime2 em $data às $horario";
                                            break 2;
                                        }
                                    }
                                }
                            }
                            echo "<div class='proximo-jogo' style='display: none;'>$proximoJogo</div>";
                        ?>
                    </td>
                </tr>
            <?php
        }
    ?>
            </tbody>
        </table>
        <div id="cardInfo">
            Detalhes
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                let timer;
                const card = document.getElementById("cardInfo");
                const isMobile = window.innerWidth <= 768;

                function showCard(e, row) {
                    const cardWidth = 300;
                    const cardHeight = 300;

                    let left = e.clientX + 10;
                    let top = e.clientY + 10;

                    if ((left + cardWidth) > window.innerWidth) {
                        left = window.innerWidth - cardWidth - 10;
                    }

                    if ((top + cardHeight) > window.innerHeight) {
                        top = window.innerHeight - cardHeight - 10;
                    }

                    const infoClube = row.querySelector(".coluna-clube")?.innerHTML || "Sem info";
                    const ultimosResultados = row.querySelector(".ultimos-resultados")?.innerHTML || "Sem resultados";
                    const proximoJogo = row.querySelector(".proximo-jogo")?.innerText || "Sem próximos jogos";

                    console.log("✅ Exibindo card:", infoClube);

                    card.innerHTML = `
                <div style="font-weight: bold; margin-bottom: 8px;">${infoClube}</div>
                <div style="margin-bottom: 8px;">Últimos 5 resultados:</div>
                <div>${ultimosResultados}</div>
                <div style="margin-top: 10px;">Próximo jogo: ${proximoJogo}</div>
            `;

                    card.style.display = "block";

                    if (isMobile) {
                        card.style.position = "fixed";
                        card.style.left = "50%";
                        card.style.top = "50%";
                        card.style.transform = "translateX(-50%)";
                    } else {
                        card.style.position = "absolute";
                        card.style.left = `${left + window.scrollX}px`;
                        card.style.top = `${top + window.scrollY}px`;
                        card.style.transform = "none";
                    }
                }

                document.querySelectorAll(".labels-tabela").forEach(row => {
                    if (isMobile) {
                        row.addEventListener("click", (e) => {
                            e.stopPropagation(); // impede o clique de chegar no document e fechar o card
                            showCard(e, row);
                        });
                    } else {
                        row.addEventListener("mouseenter", (e) => {
                            timer = setTimeout(() => {
                                showCard(e, row);
                            }, 600);
                        });

                        row.addEventListener("mouseleave", () => {
                            clearTimeout(timer);
                            card.style.display = "none";
                        });
                    }
                });

                if (isMobile) {
                    document.addEventListener("click", function (e) {
                        const isClickInsideCard = card.contains(e.target);
                        const isLabel = e.target.closest(".labels-tabela");

                        if (!isClickInsideCard && !isLabel) {
                            card.style.display = "none";
                        }
                    });
                }
            });
        </script>
    </main>
</body>

</html>