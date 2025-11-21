<div id="swagger-ui"></div>
<script src="../../assets/swagger/swagger-ui-bundle.js"></script>
<script src="../../assets/swagger/swagger-ui-standalone-preset.js"></script>
<script>
    window.onload = () => {
        SwaggerUIBundle({
            url: "../../../openapi.cache.json",
            dom_id: "#swagger-ui",
            presets: [
                SwaggerUIBundle.presets.apis,
                SwaggerUIStandalonePreset
            ],
            layout: "StandaloneLayout"
        });
    };
</script>