<script lang="ts">
    import Layout from "./Layout.svelte";
    import { onMount } from "svelte";
    import axiosInstance from "axios";
    import {
        Button,
        Card,
        Table,
        Input,
        TableBody,
        TableBodyCell,
        TableBodyRow,
        TableHead,
        TableHeadCell,
        Badge,
    } from "flowbite-svelte";
    import { Select, Label } from "flowbite-svelte";
    import { page } from "@inertiajs/svelte";
    import Create from "@C/Iuran/Modals/Create.svelte";
    import Detail from "@C/Iuran/Modals/Detail.svelte";

    const axios = axiosInstance.create();

    let role: string = $page.props.auth.user.role;
    let rt_id: string = $page.props.auth.user.rt_id;
    let hiddenAction: string;
    let value: string;
    let disabledSelectRT: boolean = false;
    let selected: string;
    let addDues: boolean = false;
    let builder = {};
    let detailModal: boolean = false;

    if (role === "RT") {
        disabledSelectRT = true;
        value = rt_id;
    } else {
        hiddenAction = "hidden";
        value = "";
    }

    interface Civil {
        id: number;
        nik: string;
        fullName: string;
        birthplace: string;
        birthdate: number;
        residentstatus: string;
        nkk: string;
        isFamilyHead: number;
        rt_id: number;
        address: string;
        status: string;
        phone: string;
        religion: string;
        job: string;
        created_at: string;
        created_by: number;
        updated_at: string | null;
        updated_by: number | null;
        deleted_at: string | null;
        deleted_by: number | null;
    }

    interface RT {
        id: number;
        leader_id: number | null;
        created_at: string;
        created_by: number;
        number: number;
        updated_at: string | null;
        updated_by: number | null;
        deleted_at: string | null;
        deleted_by: number | null;
        civils: Civil[];
    }

    interface RTFormatted {
        id: number;
        name: string;
        number: number;
    }

    let dataRT: RTFormatted[] = [];
    let data: any;
    let duesRt: any;

    const getData = async (filter: string) => {
        try {
            const response = await axios.get(
                `/api/civilian/head/${encodeURIComponent(filter)}`,
                {
                    headers: {
                        Accept: "application/json",
                    },
                },
            );

            return response.data;
        } catch (error) {
            console.error(error);
        }
    };

    const initData = async () => {
        value = $page.props.auth.user.rt_id;
        dataRT = await getRTData();
        data = await getData(value);

        if (value) duesRt = await getDuesTypes(value);
    };

    const rebuild = async () => {
        await initData();
        builder = {};
    };

    const getDuesTypes = async (filter: string) => {
        try {
            const response = await axios.get(
                `/api/dues/types/${encodeURIComponent(filter)}`,
                {
                    headers: {
                        Accept: "application/json",
                    },
                },
            );

            return response.data;
        } catch (error) {
            console.error(error);
        }
    };

    const getRTData = async (): Promise<RTFormatted[]> => {
        const response = await axios.get("/api/rt/dd", {
            headers: {
                Accept: "*/*",
            },
        });

        // console.log("API RT Data Response:", response.data);

        if (Array.isArray(response.data.data)) {
            return response.data.data.map((rt: RT) => ({
                id: rt.id,
                name: `RT ${rt.number}`,
                number: rt.number,
            }));
        } else {
            throw new Error("Unexpected response format");
        }
    };

    onMount(async () => {
        try {
            await initData();
            // console.log(dataRT);
            // console.log(data);
        } catch (error) {
            console.error("Error fetching data:", error);
        }
    });

    const handleSelectChange = async () => {
        if (!value) return;

        const response = await getData(value);
        duesRt = await getDuesTypes(value);
        data = response;
    };
</script>

<Layout>
    <div class="grid grid-cols-2 max-md:grid-cols-1 gap-2 max-md:gap-0">
        <div
            class="bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-lg border border-gray-200 dark:border-gray-700 divide-gray-200 dark:divide-gray-700 shadow-md flex flex-col p-4 sm:p-6 w-full mb-2"
        >
            <h5
                class="text-xl font-bold leading-none text-gray-900 dark:text-white mb-4"
            >
                Filter Data Pembayaran Penduduk
            </h5>
            <div
                class="block md:flex-row mx-auto justify-around items-start md:items-center min-w-full"
            >
                <div class="mb-2 md:mb-0">
                    <Label for="select-rt" class="mr-2 lg:mb-1">RT</Label>
                    <Select
                        class="w-full"
                        id="select-rt"
                        placeholder="Pilih RT"
                        bind:value
                        disabled={disabledSelectRT}
                        on:change={handleSelectChange}
                    >
                        {#each dataRT as { id, name, number }, idx}
                            {#if id == $page.props.auth.user.rt_id}
                                <option value={id} selected>{name}</option>
                            {:else}
                                <option value={id}>{name}</option>
                            {/if}
                        {/each}
                    </Select>
                </div>
                <div class="mt-2">
                    <Label for="nama_warga" class="mr-2 lg:mb-1"
                        >Cari warga</Label
                    >
                    <Input
                        class="mr-4 w-full"
                        type="text"
                        id="nama_warga"
                        placeholder="Cari Nama Warga"
                    />
                </div>
                <div class="text-end">
                    <Button class="mt-2">Cari</Button>
                </div>
            </div>
        </div>
        <div
            class="bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-lg border border-gray-200 dark:border-gray-700 divide-gray-200 dark:divide-gray-700 shadow-md w-full max-md:mb-5 mb-2"
        >
            <div class="flex justify-between px-4 py-4 items-center">
                <h5
                    class="text-xl font-bold leading-none text-gray-900 dark:text-white"
                >
                    List Iuran
                </h5>
                <Button
                    class={hiddenAction}
                    size="sm"
                    on:click={() => {
                        addDues = true;
                    }}>Tambah Kategori</Button
                >
            </div>
            <div class="block w-full">
                <div class="lg:px-4 mb-2 md:mb-0">
                    {#if duesRt || duesRt?.data?.lenght > 0}
                        <Table tableClass="mb-0">
                            <TableHead>
                                <TableHeadCell>Nama</TableHeadCell>
                                <TableHeadCell>Jumlah Iuran</TableHeadCell>
                                <TableHeadCell>Status</TableHeadCell>
                                <TableHeadCell>
                                    <span class="sr-only">Action</span>
                                </TableHeadCell>
                            </TableHead>
                            <TableBody tableBodyClass="divide-y">
                                {#each duesRt.data as item, idx}
                                    <TableBodyRow>
                                        <TableBodyCell
                                            >{item.typeDues}</TableBodyCell
                                        >
                                        <TableBodyCell
                                            >Rp. {item.amt_dues}</TableBodyCell
                                        >
                                        <TableBodyCell>
                                            {#if item.status == 1}
                                                <Badge color="green"
                                                    >Aktif</Badge
                                                >
                                            {:else}
                                                <Badge color="red"
                                                    >Tidak Aktif</Badge
                                                >
                                            {/if}
                                        </TableBodyCell>
                                        <TableBodyCell tdClass="divide-y">
                                            <Button
                                                type="button"
                                                color="blue"
                                                on:click={() => {
                                                    selected = item.id;
                                                    detailModal = true;
                                                }}
                                            >
                                                Detail
                                            </Button>
                                        </TableBodyCell>
                                    </TableBodyRow>
                                {/each}
                            </TableBody>
                        </Table>
                    {:else}
                        <p>RT tidak memiliki iuran aktif</p>
                    {/if}
                </div>
            </div>
        </div>
    </div>

    <div
        class="bg-white dark:bg-gray-800 text-gray-500 dark:text-gray-400 rounded-lg border border-gray-200 dark:border-gray-700 divide-gray-200 dark:divide-gray-700 shadow-md w-full max-md:mb-5 mb-3"
    >
        <Table tableClass="mb-0">
            <TableHead>
                <TableHeadCell>Nama Lengkap</TableHeadCell>
                <TableHeadCell>Alamat</TableHeadCell>
                <TableHeadCell class="text-center"
                    >Status Kependudukan</TableHeadCell
                >
                <TableHeadCell>
                    <span class="sr-only">Edit</span>
                </TableHeadCell>
            </TableHead>
            <TableBody tableBodyClass="divide-y">
                {#if data}
                    {#each data.data as item, idx}
                        <TableBodyRow>
                            <TableBodyCell>{item?.fullName}</TableBodyCell>
                            <TableBodyCell>{item.address}</TableBodyCell>
                            <TableBodyCell class="text-center">
                                <Badge color="green">Tetap</Badge>
                            </TableBodyCell>
                            <TableBodyCell>
                                <Button
                                    href={`/iuran/detail?rt=${encodeURIComponent(item.rt_id.id)}&civ=${encodeURIComponent(item.id)}`}
                                    >Detail</Button
                                >
                            </TableBodyCell>
                        </TableBodyRow>
                    {/each}
                {/if}
            </TableBody>
        </Table>
        <div class="mb-3"></div>
    </div>
</Layout>

<Create bind:showState={addDues} on:comp={rebuild} />
{#if selected && detailModal}
    <Detail
        bind:showState={detailModal}
        bind:items={selected}
        on:comp={rebuild}
    />
{/if}
<!-- {#if selected}

{/if} -->
