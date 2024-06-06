<script lang="ts">
    import axiosInstance from "axios";

    import {
        Modal,
        Input,
        Label,
        Button,
        Select,
        Toast,
    } from "flowbite-svelte";
    import { CheckCircleSolid, CloseCircleSolid } from "flowbite-svelte-icons";

    import { createForm } from "felte";
    import { validator } from "@felte/validator-zod";
    import {
        type ChangeRWSchema,
        changeRWSchema,
    } from "@R/Utils/Schema/User/ChangeRW";

    import { createEventDispatcher, onMount } from "svelte";
    import { page, router } from "@inertiajs/svelte";

    const dispatch = createEventDispatcher();
    const axios = axiosInstance.create({ withCredentials: true });

    export let showState = false;
    export let target: string = "";

    let qualifiedCandidates: any[] | undefined = [];
    let err: { status: null | boolean; message: null | string } = {
        status: null,
        message: null,
    };
    let currentRW: any;

    const logout = async () => {
        await router.get("/api/auth/signout");
    };

    const getRW = async () => {
        try {
            const response = await axios.get(`/api/user/rw/current`);
            return response.data;
        } catch (error) {
            console.error(error);
        }
    };

    const getCivils = async () => {
        const response = await axios.get("/api/user");

        return response.data;
    };

    const filterCandidates = async () => {
        const civils = await getCivils();

        if (civils.data.length < 1) return;

        let resultArray: any[] = [];

        civils.data.map((item: any) => {
            resultArray.push(item);
        });

        return resultArray;
    };

    const { form, isSubmitting, errors } = createForm<ChangeRWSchema>({
        extend: validator<ChangeRWSchema>({
            schema: changeRWSchema,
        }),
        onSubmit: async (values) => {
            const response = await axios.put("/api/user", {
                id: values.id,
                role: "RW",
                _token: $page.props.csrf_token,
            });

            err = response.data;
            showState = false;
            dispatch("comp");

            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            await logout();
        },
        onError: (values: unknown) => {
            console.error(values?.response);
            err = values?.response?.data;

            showState = false;
            setTimeout(() => {
                err = { status: null, message: null };
            }, 5000);

            return;
        },
        onSuccess: (response) => {
            console.log("Success");
        },
    });

    const buildOptions = async () => {
        const data = await filterCandidates();

        qualifiedCandidates = [];

        data?.map((value: any) => {
            qualifiedCandidates?.push({
                value: value.id,
                name: `${value.civilian_id.fullName} | RT. ${value.civilian_id.rt_id.number}`,
            });
        });

        return qualifiedCandidates;
    };

    const initPage = async () => {
        currentRW = (await getRW()).data;
        await buildOptions();
    };

    onMount(async () => {
        await initPage();
    });
</script>

<Modal
    title="Ganti RW"
    bind:open={showState}
    on:close={() => {
        currentRW = null;
    }}
>
    <!-- {#await buildOptions() then ops} -->
    <form method="POST" use:form>
        <div class="grid grid-cols-1s md:gap-6">
            <div class="mb-4">
                <Label for="leader" class="mb-2">Kandidat RW</Label>
                <Select
                    class="my-2"
                    size="sm"
                    id="leader"
                    placeholder="Ketua RT"
                    name="id"
                >
                    {#if qualifiedCandidates}
                        {#each qualifiedCandidates as { value, name }, idx}
                            {#if value == currentRW.id}
                                <option {value} selected>{name}</option>
                            {:else}
                                <option {value}>{name}</option>
                            {/if}
                        {/each}
                    {/if}
                </Select>

                {#if $errors.id}
                    <span class="text-sm text-red-500">{$errors.id}</span>
                {/if}
            </div>
            <div class="mb-4 hidden">
                <Label for="number" class="mb-2">ID</Label>
                <Input type="number" name="id" readonly value={target} />
                {#if $errors.id}
                    <span class="text-sm text-red-500">{$errors.id}</span>
                {/if}
            </div>
        </div>
        <div class="block flex">
            <Button type="submit" class="ml-auto" disabled={$isSubmitting}
                >Simpan</Button
            >
        </div>
    </form>
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
