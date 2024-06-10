<script lang="ts">
    export let imageUrl: string | undefined = undefined;
    export let title: string | null | undefined = undefined;
    export let description: string | null | undefined = undefined;
    export let hrefUrl: string | null | undefined = undefined;
    export let date: string | null | undefined = undefined;
    export let classCard: string | null | undefined = undefined;

    import { CalendarMonthOutline } from "flowbite-svelte-icons";
</script>

<a href={hrefUrl} class="rounded">
    <div
        class="card flex flex-col md:flex-row bg-gray-900 rounded-xl {classCard}"
    >
        <div class="card-image md:min-w-72 md:max-w-72 md:min-h-80">
            <img
                src={imageUrl}
                alt={title}
                class="rounded-t-xl md:rounded-l-xl md:rounded-tr-none object-cover h-full w-full"
            />
        </div>
        <div class="card-content p-4">
            <div
                class="bg-white p-3 h-full rounded-lg flex flex-col justify-center"
            >
                <h5 class="text-2xl font-bold mb-3">{title}</h5>
                <p class="text-current mb-2 text-gray-500">{description}</p>
                <div class="flex items-center text-gray-500">
                    <CalendarMonthOutline class="mr-3 text-black" />
                    {date}
                </div>
            </div>
        </div>
    </div>
</a>
