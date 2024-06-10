<script lang="ts">
    import { Modal } from "flowbite-svelte";

    import axiosInstance from "axios";

    const axios = axiosInstance.create({ withCredentials: true });

    export let items: any;
    export let showState = false;

    let defaultImg = `https://cdn1.epicgames.com/ue/product/Screenshot/Screenshot23-1920x1080-a2f8a3c1f88f3b5716bdd8c9a2ea0c28.jpg?resize=1&w=1920`;

    const getNewsData = async (id: string = "") => {
        const response = await axios.get(
            `/api/news/${encodeURIComponent(id)}`,
            {
                headers: {
                    Accept: "application/json",
                },
            },
        );

        console.log(response.data);

        return response.data;
    };
</script>

<Modal title="Preview Pengumuman" bind:open={showState} autoclose>
    {#await getNewsData(items) then item}
        <img
            src={item.data.attachment
                ? `/storage/assets/uploads/${item.data.attachment}`
                : defaultImg}
            alt=""
            class="w-full h-auto max-h-72 mb-3 rounded-lg border-2 border-gray-500"
        />
        <h5
            class="mb-4 text-xl font-bold tracking-tight text-gray-900 dark:text-white"
        >
            {item.data.title}
        </h5>
        <p class="overflow-y-scroll">{item.data.desc}</p>
    {/await}
</Modal>
