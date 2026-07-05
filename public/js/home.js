document.addEventListener("alpine:init", function () {
    Alpine.data("counter", function () {
        return {
            count: 0,
            increment: function () {
                this.count += 1;
            },
        };
    });
});
