<script lang="ts">
    import { Input, Label, Modal, Select, Textarea } from "flowbite-svelte";

    import axiosInstance from "axios";

    const axios = axiosInstance.create({ withCredentials: true });

    export let items: any;
    export let showState = false;

    const getNewsData = async (id: string = "") => {
        const response = await axios.get(
            `/api/spending/${encodeURIComponent(id)}`,
            {
                headers: {
                    Accept: "application/json",
                },
            },
        );

        return response.data;
    };

    const categoty = [
        { name: "Acara", value: "Event" },
        { name: "Administrasi", value: "Administration" },
        { name: "Infrastruktur/Pembangunan", value: "Infrastructure" },
    ];
</script>

<Modal title="Preview Pengambilan Dana" bind:open={showState} autoclose>
    {#await getNewsData(items) then item}
        <div class="mb-4">
            <Label for="category" class="mb-2">Kategori</Label>
            <Select
                id="category"
                name="category"
                placeholder="Kategori"
                items={categoty}
                value={item.data.category}
                disabled
            />
        </div>
        <div class="mb-4">
            <Label for="desc" class="mb-2">Keterangan</Label>
            <Textarea
                rows="2"
                id="desc"
                name="desc"
                placeholder="Isi Pengumuman"
                value={item.data.desc}
                disabled
            />
        </div>
        <div class="mb-4">
            <Label for="amount" class="mb-2">Jumlah</Label>
            <Input
                type="number"
                id="amount"
                name="amount"
                placeholder="Isi Pengumuman"
                value={item.data.amount}
                disabled
            />
        </div>
    {/await}
</Modal>
