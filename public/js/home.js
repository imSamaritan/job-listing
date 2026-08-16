document.addEventListener("alpine:init", function () {
    Alpine.data("counter", function () {
        return {
            count: 0,
            increment: function () {
                console.log("Make a reques")
            },
        };
    });
});
