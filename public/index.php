<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Article finder</title>
    <link rel="shortcut icon" href="assets/favicon.png">
    <style type="text/css">@import url("../src/styles/index.css");</style>
</head>
<body>
    <?php
        function sort_by_orden ($a, $b) {
            return $a["size"] - $b["size"];
        }

        echo "<h1 class='pageTitle'>Article finder</h1>";
        include("../src/components/Searcher.html");

        echo "<hr/>";

        echo "<div class='results'>";

            if(isset( $_GET["inputSearch"] )) {
                $searchPage = $_GET["inputSearch"];
                $orderBy = $_GET["orderResults"];
                $endPoint = "https://es.wikipedia.org/w/api.php";
                $params = [
                    "action" => "query",
                    "list" => "search",
                    "srsearch" => $searchPage,
                    "format" => "json",
                    "srlimit" => "12"
                ];

                switch ($orderBy) {
                    case "relevance":
                        $params += ["srsort" => "relevance"];
                        break;
                    case "date":
                        $params += ["srsort" => "create_timestamp_desc"];
                        break;
                    default:
                        $params += ["srsort" => "relevance"];
                }

                $url = $endPoint . "?" . http_build_query( $params );

                $ch = curl_init( $url );
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                $output = curl_exec( $ch );
                curl_close( $ch );
                $result = json_decode( $output, true );

                if($orderBy == "size") {
                    usort($result["query"]["search"], "sort_by_orden");
                }

                foreach($result["query"]["search"] as $article) {
                    echo "<a class='results-link' href='https://es.wikipedia.org/?curid=" . $article["pageid"] . "') >";
                    echo "<div class='results-card'>";
                    echo "<h2>" . $article["title"] . " </h2>";
                    echo "<p>" . $article["snippet"] . " </p>"; 
                    echo "</div></a>";
                }
            } else {
                echo "<p>Tus resultados se mostrarán aquí</p>";
            }

        echo "</div>";
    ?>
</body>
</html>