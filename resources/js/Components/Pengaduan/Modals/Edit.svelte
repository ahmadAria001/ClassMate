<script lang="ts">
    import {
        Button,
        Input,
        Label,
        Modal,
        Textarea,
        Toast,
    } from "flowbite-svelte";

    import axiosInstance from "axios";
    import { page } from "@inertiajs/svelte";

    import { createForm } from "felte";
    import { validator } from "@felte/validator-zod";

    import {
        CheckCircleSolid,
        CloseCircleSolid,
        QuestionCircleSolid,
    } from "flowbite-svelte-icons";
    import { createEventDispatcher, onMount } from "svelte";
    import { Popover } from "flowbite-svelte";

    export let showState = false;
    export let target: string;

    const axios = axiosInstance.create({ withCredentials: true });

    let data: any;
    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };
    const dispatch = createEventDispatcher();
    let selectedImage: any | null = null;
    let imgContent: File;

    const submitChange = async (values: any) => {
        try {
            console.log(values);
            let body: any;

            if (values.attachment) {
                const formData = new FormData();
                formData.append("id", values.id);
                formData.append("description", values.docs_id.description);
                formData.append("complaintStatus", values.complaintStatus);
                formData.append("attachment", imgContent);
                formData.append("_method", "PUT");
                formData.append("_token", $page.props.csrf_token);

                body = formData;
            } else {
                body = {
                    id: values.id,
                    description: values.docs_id.description,
                    complaintStatus: values.complaintStatus,
                    _method: "PUT",
                    _token: $page.props.csrf_token,
                };
            }
            const response = await axios.post("/api/docs/complaint", body);
            console.log(response.data);

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

            if (values?.response?.status == 401) {
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

    const getComplaints = async (id: string = "") => {
        const response = await axios.get(`/api/docs/complaint/${id}`, {
            headers: {
                Accept: "*/*",
            },
        });
        return response.data;
    };

    const getAsset = async (fileName: string) => {
        const response = await axios.get(`/assets/uploads/${fileName}`, {
            responseType: "blob",
        });

        const fileInput = document.getElementById(
            "dropzone-file",
        ) as HTMLInputElement;
        const file = new File([response.data], fileName, {
            type: response.data.type,
        });

        const dt = new DataTransfer();
        dt.items.add(file);

        imgContent = file;

        if (fileInput) fileInput.files = dt.files;
    };

    const initialLoad = async (filter: string) => {
        const data = await getComplaints(filter);
        return data;
    };

    onMount(async () => {
        const response = await getComplaints(target);
        data = await response.data;

        await getAsset(data.attachment);
    });
</script>

<Modal
    title="Detail Pengaduan"
    bind:open={showState}
    on:close={() => (selectedImage = null)}
>
    {#await initialLoad(target) then item}
        <div class="mb-4">
            <Label for="full_name" class="mb-2">Nama Pelapor</Label>
            <Input
                id="full_name"
                placeholder="Nama Pelapor"
                readonly
                value={item.data.created_by.civilian_id.fullName}
            />
        </div>
        <div class="grid md:grid-cols-2 md:gap-6">
            <div class="mb-4">
                <Label for="no_hp" class="mb-2">No HP</Label>
                <Input
                    id="no_hp"
                    placeholder="No HP"
                    readonly
                    value={item.data.created_by.civilian_id.phone}
                />
            </div>
            <div class="mb-4">
                <Label for="address" class="mb-2">Alamat</Label>
                <div class="flex gap-2">
                    <Input
                        id="address"
                        placeholder="Alamat"
                        readonly
                        value={item.data.created_by.civilian_id.address}
                    />
                    <div class="flex align-middle w-fit h-full py-2">
                        <QuestionCircleSolid
                            class="w-6 h-6 dark:fill-white"
                            id="pass-hint"
                        />
                    </div>
                </div>

                <Popover
                    class="w-64 text-sm text-white"
                    title="Alamat"
                    triggeredBy={`#pass-hint`}
                >
                    {item.data.created_by.civilian_id.address}
                </Popover>
            </div>
        </div>
        <div class="mb-4">
            <Label for="timeUpload" class="mb-2">Waktu Dikirim</Label>
            <Input
                id="timeUpload"
                placeholder="Waktu Dikirim"
                readonly
                value={new Date(item.data.created_at).toLocaleDateString()}
            />
        </div>

        <div class="mb-4">
            <Label for="desc" class="mb-2 text-lg">Permasalahan</Label>
            <Textarea
                rows="2"
                id="desc"
                name="description"
                placeholder="Isi Pengumuman"
                readonly
                value={item.data.docs_id.description}
            />
        </div>
        <div class="mb-4">
            <img
                src={selectedImage
                    ? selectedImage
                    : `/assets/uploads/${item.data.attachment}`}
                alt="Selected Image"
                class={item.data.attachment || selectedImage
                    ? "w-full h-auto max-h-72 mb-3 rounded-lg border-2 border-gray-500"
                    : "hidden"}
            />
        </div>

        <div class="block flex">
            <div class="ml-auto">
                <Button
                    type="button"
                    class="mr-3"
                    color="red"
                    on:click={async () => {
                        data.complaintStatus = "Unresolved";
                        await submitChange(data);
                    }}>Tolak Pengaduan</Button
                >
                <Button
                    type="button"
                    name="complaintStatus"
                    on:click={async () => {
                        data.complaintStatus = "Resolved";
                        await submitChange(data);
                    }}>Proses Pengaduan</Button
                >
            </div>
        </div>
    {/await}
</Modal>

{#if err.status != null && err.status == true}
    <Toast color="green" class="fixed top-10 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CheckCircleSolid class="w-5 h-5" />
            <span class="sr-only">Check icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
{#if err.status != null && err.status == false}
    <Toast color="red" class="fixed top-10 right-1 z-[50000]">
        <svelte:fragment slot="icon">
            <CloseCircleSolid class="w-5 h-5" />
            <span class="sr-only">Error icon</span>
        </svelte:fragment>
        {err.message}
    </Toast>
{/if}
