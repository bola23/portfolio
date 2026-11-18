<x-layouts.site title="Page not found" :noindex="true">
    <section class="relative isolate flex min-h-[80svh] items-center overflow-hidden pt-28">
        <div class="blob top-1/4 left-1/3 -z-10 size-[380px] bg-primary" aria-hidden="true"></div>
        <div class="container-page text-center">
            <p class="text-gradient font-display text-8xl font-extrabold">404</p>
            <h1 class="mt-4 text-3xl font-extrabold sm:text-4xl">Page not found</h1>
            <p class="mx-auto mt-4 max-w-md text-muted">The page you’re looking for doesn’t exist or was moved. Lost in the stack? Let’s get you back.</p>
            <div class="mt-10 flex flex-wrap justify-center gap-3">
                <a href="{{ route('home') }}" class="btn btn-primary">Back to home</a>
                <a href="{{ route('projects.index') }}" class="btn btn-outline">Browse projects</a>
            </div>
        </div>
    </section>
</x-layouts.site>
