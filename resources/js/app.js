require("./bootstrap");

$("#language").change(function() {
    location = `/locale/${$(this).val()}`;
});
