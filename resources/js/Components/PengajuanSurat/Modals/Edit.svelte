<script lang="ts">
    import { Button, Input, Label, Modal, Toast } from "flowbite-svelte";

    import { page } from "@inertiajs/svelte";
    import axiosInstance from "axios";
    import { createEventDispatcher, onMount } from "svelte";
    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";
    import DaftarPermintaanSurat from "@R/Pages/DaftarPermintaanSurat.svelte";

    export let target: string;
    export let showState = false;

    const axios = axiosInstance.create({ withCredentials: true });
    const dispatch = createEventDispatcher();
    let data: any | null = null;
    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };

    const getRequestDocs = async (id: string) => {
        try {
            const response = await axios.get(
                `/api/docs/request/${encodeURIComponent(id)}`,
            );

            return response.data?.data;
        } catch (error) {
            err = {
                message: error?.response?.data?.message,
                status: error?.response?.data?.status,
            };
            showState = false;
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            return null;
        }
    };

    const initData = async () => {
        data = await getRequestDocs(target);
        console.log(data);
    };

    const submitChange = async (values: any) => {
        try {
            let body = {
                id: values.id,
                requestStatus: values.requestStatus,
                _token: $page.props.csrf_token,
            };

            const response = await axios.put("/api/docs/request", body);

            err = response.data;
            showState = false;
            dispatch("comp");

            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);
        } catch (error) {
            err = {
                message: error?.response?.data?.message,
                status: error?.response?.data?.status,
            };
            showState = false;
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            console.error(error);

            if (error?.response?.status == 401) {
                err = {
                    message: "Anda tidak memiliki izin",
                    status: false,
                };
                showState = false;
                setTimeout(() => {
                    err = { status: null, message: null };
                }, 5000);
            }

            return;
        }
    };

    onMount(async () => {
        await initData();
    });
</script>

<Modal
    title="Deskripsi Surat"
    bind:open={showState}
    on:close={() => {
        dispatch("comp");
        data = null;
    }}
>
    {#if data}
        <section>
            <div class="mb-4">
                <Label for="full_name" class="mb-2">Nama Pemohon</Label>
                <Input
                    id="full_name"
                    placeholder="Nama Pemohon"
                    readonly
                    value={data.request_by.fullName}
                />
            </div>
            <div class="grid md:grid-cols-2 md:gap-6">
                <div class="mb-4">
                    <Label for="address" class="mb-2">Alamat</Label>
                    <Input
                        id="address"
                        placeholder="Alamat"
                        name="address"
                        readonly
                        value={data.request_by.address}
                    />
                </div>
                <div class="mb-4">
                    <Label for="phone" class="mb-2">No. HP</Label>
                    <Input
                        type="text"
                        readonly
                        id="phone"
                        placeholder="No. HP"
                        name="phone"
                        value={data.request_by.phone}
                    />
                </div>
            </div>

            <div class="mb-4">
                <Label for="problems" class="mb-2">Keterangan</Label>
                <Input
                    id="problems"
                    placeholder="Keterangan"
                    readonly
                    value={data.docs_id.description}
                />
            </div>
            <div class="flex justify-end gap-5">
                <Button
                    type="button"
                    color="red"
                    on:click={async () => {
                        data.requestStatus = "Declined";
                        await submitChange(data);
                    }}>Tolak</Button
                >
                <Button
                    type="button"
                    color="green"
                    on:click={async () => {
                        data.requestStatus = "Resolved";
                        await submitChange(data);
                    }}>Terima</Button
                >
            </div>
        </section>
    {/if}
</Modal>

{#if err.status != null && err.status == true}
    <Toast color="green" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CheckCircleSolid class="w-5 h-5" />
            <span class="sr-only">Check icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
{#if err.status != null && err.status == false}
    <Toast color="red" class="fixed top-3 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CloseCircleSolid class="w-5 h-5" />
            <span class="sr-only">Error icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
